<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PHPUnit\Framework\TestCase;

/**
 * Test-guardia: un controlador de `frontend/` no ejecuta una acción con una cápsula `HashB` que
 * acaba de pedir él mismo.
 *
 * La cápsula (`ctx_…`) demuestra que la acción se pidió desde la pantalla que la firmó. Si el mismo
 * controlador pide la cápsula a un endpoint `*_data` y la pasa a continuación al endpoint que
 * escribe, la acción se ejecuta con solo abrir la pantalla: así `propuestas_aprobar.php` aprobaba
 * las propuestas de encargos (y borraba sus tablas) al abrirse. La cápsula tiene que llegar en la
 * petición (formulario o enlace de la pantalla anterior) y aquí solo se reenvía.
 *
 * Análisis estático: en cada `PostRequest::getDataFromUrl('/src/<modulo>/<accion>', …)` hacia un
 * controlador que abre una cápsula, el valor de cada `ctx_…` no puede salir (siguiendo las
 * variables del fichero) de otro `PostRequest::getData…`. Fase 6 del plan de revisión de tests.
 */
final class PantallasNoFirmanSuPropiaAccionTest extends TestCase
{
    private const RAIZ = __DIR__ . '/../../../../';

    /**
     * controlador relativo a la raíz => motivo
     *
     * @var array<string, string>
     */
    private const EXCEPCIONES = [];

    public function test_ningun_controlador_frontend_se_firma_su_accion(): void
    {
        $raiz = (string) realpath(self::RAIZ) . '/';
        $fallos = [];
        $llamadasRevisadas = 0;
        foreach ($this->controladoresFrontend($raiz) as $fichero) {
            $rel = substr($fichero, strlen($raiz));
            $codigo = (string) file_get_contents($fichero);
            foreach ($this->llamadasAAcciones($raiz, $codigo) as [$endpoint, $argumento]) {
                $llamadasRevisadas++;
                foreach ($this->capsulasEnviadas($codigo, $argumento) as $nombre => $expresion) {
                    if ($this->saleDeOtraLlamada($codigo, $expresion) && !in_array($rel, $this->exceptuados(), true)) {
                        $fallos[] = "$rel: pasa a $endpoint la cápsula «{$nombre}» que acaba de pedir ($expresion)";
                    }
                }
            }
        }
        $this->assertGreaterThan(20, $llamadasRevisadas, 'el análisis tiene que encontrar las llamadas a acciones');
        $this->assertSame([], $fallos, "La cápsula tiene que venir de la petición (filter_input / \$_POST), no de un endpoint *_data llamado en el mismo controlador:\n" . implode("\n", $fallos));
    }

    /** @return list<string> */
    private function exceptuados(): array
    {
        return array_keys(self::EXCEPCIONES);
    }

    public function test_el_analisis_detecta_el_patron(): void
    {
        $raiz = (string) realpath(self::RAIZ) . '/';
        $malo = <<<'PHP'
            $ctxData = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar_data', []);
            $data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar', [
                'ctx_aprobar' => PayloadCoercion::string($ctxData['ctx_aprobar'] ?? ''),
            ]);
            PHP;
        $maloPorVariable = <<<'PHP'
            $formCtx = Soporte::fila(PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar_data', []));
            $post = (array) $_POST;
            $post['ctx_aprobar'] = (string) ($formCtx['ctx_aprobar'] ?? '');
            $data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar', $post);
            PHP;
        $bueno = <<<'PHP'
            $ctxData = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar_data', []);
            $vista = ['ctx_aprobar' => $ctxData['ctx_aprobar']];
            $ctx = (string) filter_input(INPUT_POST, 'ctx_aprobar');
            $data = PostRequest::getDataFromUrl('/src/encargossacd/propuestas_aprobar', ['ctx_aprobar' => $ctx]);
            PHP;
        $this->assertSame(1, $this->cuentaFallos($raiz, $malo));
        $this->assertSame(1, $this->cuentaFallos($raiz, $maloPorVariable));
        $this->assertSame(0, $this->cuentaFallos($raiz, $bueno));
    }

    private function cuentaFallos(string $raiz, string $codigo): int
    {
        $n = 0;
        foreach ($this->llamadasAAcciones($raiz, $codigo) as [, $argumento]) {
            foreach ($this->capsulasEnviadas($codigo, $argumento) as $expresion) {
                $n += $this->saleDeOtraLlamada($codigo, $expresion) ? 1 : 0;
            }
        }

        return $n;
    }

    /** @return list<string> */
    private function controladoresFrontend(string $raiz): array
    {
        $ficheros = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . 'frontend', \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if ($f instanceof \SplFileInfo && $f->isFile() && $f->getExtension() === 'php') {
                $ficheros[] = $f->getPathname();
            }
        }
        sort($ficheros);

        return $ficheros;
    }

    /**
     * Llamadas `PostRequest::getData…('/src/m/a', ARG)` cuyo controlador de `src/` abre una cápsula.
     *
     * @return list<array{string, string}> [endpoint, primer argumento tras la URL]
     */
    private function llamadasAAcciones(string $raiz, string $codigo): array
    {
        $llamadas = [];
        if (!preg_match_all('#PostRequest::getData(?:FromUrl)?\(\s*[\'"](/src/(\w+)/(\w+))[\'"]#', $codigo, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }
        foreach ($m as $x) {
            $controlador = $raiz . 'src/' . $x[2][0] . '/infrastructure/ui/http/controllers/' . $x[3][0] . '.php';
            if (!is_file($controlador) || !$this->abreCapsula((string) file_get_contents($controlador))) {
                continue;
            }
            $resto = substr($codigo, $x[0][1] + strlen($x[0][0]));
            $llamadas[] = [$x[1][0], $this->primerArgumento($resto)];
        }

        return $llamadas;
    }

    private function abreCapsula(string $controlador): bool
    {
        return str_contains($controlador, 'HashB::open(') || preg_match('/MutationCtx::\w+\(/', $controlador) === 1;
    }

    /** Texto del argumento que sigue a la URL, hasta la coma o el paréntesis de cierre de su nivel. */
    private function primerArgumento(string $resto): string
    {
        $resto = ltrim($resto);
        if (!str_starts_with($resto, ',')) {
            return '';
        }
        $resto = substr($resto, 1);
        $nivel = 0;
        $n = strlen($resto);
        for ($i = 0; $i < $n; $i++) {
            $c = $resto[$i];
            if ($c === '(' || $c === '[') {
                $nivel++;
            } elseif ($c === ')' || $c === ']') {
                if ($nivel === 0) {
                    break;
                }
                $nivel--;
            } elseif ($c === ',' && $nivel === 0) {
                break;
            }
        }

        return trim(substr($resto, 0, $i));
    }

    /**
     * Las cápsulas que viajan en el argumento: `'ctx_x' => EXPR` si es un array literal; si es una
     * variable, lo mismo en sus asignaciones y en `$var['ctx_x'] = EXPR`.
     *
     * @return array<string, string> nombre de la cápsula => expresión
     */
    private function capsulasEnviadas(string $codigo, string $argumento): array
    {
        $textos = [$argumento];
        if (preg_match('/^\$(\w+)$/', $argumento, $m)) {
            $textos = $this->asignaciones($codigo, $m[1]);
            if (preg_match_all('/\$' . $m[1] . '\[\s*[\'"](ctx_\w+)[\'"]\s*\]\s*=\s*([^;]+);/', $codigo, $mm, PREG_SET_ORDER)) {
                foreach ($mm as $x) {
                    $textos[] = "'" . $x[1] . "' => " . $x[2] . ',';
                }
            }
        }
        $capsulas = [];
        foreach ($textos as $texto) {
            if (preg_match_all('/[\'"](ctx_\w+)[\'"]\s*=>\s*([^\n]+)/', $texto, $mm, PREG_SET_ORDER)) {
                foreach ($mm as $x) {
                    $capsulas[$x[1]] = rtrim(trim($x[2]), ',');
                }
            }
        }

        return $capsulas;
    }

    /** ¿La expresión viene, siguiendo sus variables, de un `PostRequest::getData…`? */
    private function saleDeOtraLlamada(string $codigo, string $expresion, int $nivel = 0): bool
    {
        if (str_contains($expresion, 'PostRequest::getData')) {
            return true;
        }
        if ($nivel >= 3 || !preg_match_all('/\$(\w+)/', $expresion, $m)) {
            return false;
        }
        foreach (array_unique($m[1]) as $variable) {
            if (in_array($variable, ['_POST', '_GET', '_REQUEST'], true)) {
                continue;
            }
            foreach ($this->asignaciones($codigo, $variable) as $asignacion) {
                if ($this->saleDeOtraLlamada($codigo, $asignacion, $nivel + 1)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lo que se asigna a `$variable` en el fichero (el texto hasta el `;`).
     *
     * @return list<string>
     */
    private function asignaciones(string $codigo, string $variable): array
    {
        if (!preg_match_all('/\$' . preg_quote($variable, '/') . '\s*=(?!=)\s*([^;]+);/', $codigo, $m)) {
            return [];
        }

        return $m[1];
    }
}
