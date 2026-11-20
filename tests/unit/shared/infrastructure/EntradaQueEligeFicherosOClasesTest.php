<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\DatosInfoRepoResolver;

/**
 * Lo que llega en la petición no elige qué fichero se lee, qué clase se carga ni qué script se ejecuta
 * (revisión de seguridad del 2026-11-20, FIC-01, FIC-03, FIC-04 y FIC-10).
 */
final class EntradaQueEligeFicherosOClasesTest extends TestCase
{
    private const RAIZ = __DIR__ . '/../../../..';

    public function test_no_existe_el_lector_de_ficheros_por_parametro(): void
    {
        $this->assertFileDoesNotExist(self::RAIZ . '/frontend/shared/parsedown.php');
    }

    /** @return iterable<string, array{string}> */
    public static function clasesNoAdmitidas(): iterable
    {
        yield 'script de cli' => ['src\ubis\infrastructure\cli\centros_resincronizar'];
        yield 'controlador' => ['src\usuarios\infrastructure\ui\http\controllers\usuario_guardar'];
        yield 'clase de aplicación' => ['src\devel_db_admin\application\MoverTabla'];
        yield 'clase de vendor' => ['GuzzleHttp\Client'];
        yield 'clase del frontend' => ['frontend\shared\web\Lista'];
        yield 'Info fuera de domain' => ['src\ubis\application\InfoAlgo'];
        yield 'con puntos' => ['src\ubis\domain\..\Info'];
        yield 'vacía' => [''];
    }

    #[DataProvider('clasesNoAdmitidas')]
    public function test_solo_se_resuelven_clases_info_de_un_dominio_y_sin_cargar_otra_cosa(string $clase): void
    {
        $this->assertFalse(DatosInfoRepoResolver::esNombreAdmitido($clase));
        try {
            DatosInfoRepoResolver::resolve($clase);
            $this->fail('debería rechazarse');
        } catch (InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }
        if ($clase === 'src\ubis\infrastructure\cli\centros_resincronizar') {
            $cargados = array_map(static fn (string $f): string => basename($f), get_included_files());
            $this->assertNotContains('centros_resincronizar.php', $cargados, 'el nombre no llega al autoload');
        }
    }

    public function test_los_nombres_de_las_clases_info_reales_se_admiten(): void
    {
        $this->assertTrue(DatosInfoRepoResolver::esNombreAdmitido('src\ubis\domain\InfoDelegaciones'));
        $this->assertTrue(DatosInfoRepoResolver::esNombreAdmitido('\src\dossiers\domain\Info3010'));
        foreach (glob(self::RAIZ . '/src/*/domain/Info*.php') ?: [] as $f) {
            $clase = 'src\\' . basename(dirname($f, 2)) . '\domain\\' . basename($f, '.php');
            $this->assertTrue(DatosInfoRepoResolver::esNombreAdmitido($clase), $clase);
        }
    }

    public function test_dossiers_no_comprueba_la_clase_por_su_cuenta(): void
    {
        $codigo = (string) file_get_contents(self::RAIZ . '/src/dossiers/application/DossiersVerPantallaData.php');

        $this->assertDoesNotMatchRegularExpression('/class_exists\(\$obj\)/', $codigo);
    }

    /** @return iterable<string, array{string}> */
    public static function scriptsDeCli(): iterable
    {
        foreach (glob(self::RAIZ . '/src/*/infrastructure/cli/*.php') ?: [] as $f) {
            if (preg_match('/^namespace\s+[\w\\\\]+;/m', (string) file_get_contents($f)) !== 1) {
                yield basename($f) => [$f];
            }
        }
    }

    #[DataProvider('scriptsDeCli')]
    public function test_un_script_de_cli_solo_corre_en_la_terminal_o_desde_su_controlador(string $fichero): void
    {
        $codigo = (string) file_get_contents($fichero);
        // Lo primero que ejecuta, antes de leer nada de la petición.
        $sinComentarios = (string) preg_replace('~/\*.*?\*/|//[^\n]*~s', '', $codigo);
        $this->assertMatchesRegularExpression(
            "/^<\\?php\\s*(declare\\(strict_types=1\\);\\s*)?if \\(PHP_SAPI !== 'cli'( && !defined\\('ORBIX_CLI_DESDE_CONTROLADOR'\\))?\\) \\{/",
            $sinComentarios
        );
    }

    public function test_los_controladores_espejo_marcan_la_llamada_despues_de_comprobar_permiso_y_capsula(): void
    {
        foreach (glob(self::RAIZ . '/src/*/infrastructure/ui/http/controllers/*.php') ?: [] as $f) {
            $codigo = (string) file_get_contents($f);
            if (!str_contains($codigo, "/cli/")) {
                continue;
            }
            $posMarca = strpos($codigo, "|| define('ORBIX_CLI_DESDE_CONTROLADOR'");
            $this->assertNotFalse($posMarca, basename($f));
            $this->assertGreaterThan((int) strrpos(substr($codigo, 0, (int) strpos($codigo, 'require __DIR__')), 'return;'), $posMarca, basename($f));
        }
    }

    public function test_el_proxy_de_src_solo_admite_rutas_modulo_accion(): void
    {
        $codigo = (string) file_get_contents(self::RAIZ . '/frontend/shared/controller/src_ajax.php');

        $this->assertStringNotContainsString('[A-Za-z0-9_./-]+', $codigo);
    }

    public function test_ningun_documento_se_copia_a_un_directorio_que_sirve_el_servidor_web(): void
    {
        $malos = [];
        foreach (['src', 'frontend'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::RAIZ . '/' . $dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $f) {
                /** @var \SplFileInfo $f */
                if (in_array($f->getExtension(), ['php', 'phtml', 'twig', 'inc'], true)
                    && str_contains((string) file_get_contents($f->getPathname()), 'log/tmp')
                ) {
                    $malos[] = $dir . '/' . $it->getSubPathname();
                }
            }
        }

        $this->assertSame([], $malos, 'los PDF se enseñan desde memoria o con una descarga firmada, no desde log/tmp');
    }
}
