<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;
use Tests\support\DetectaEscriturasEnBd;
use Tests\support\UbiscamasEnDlInstalada;

/**
 * Test-guardia: un controlador de lectura de `src/` llamado sin parámetros (POST vacío) responde
 * siempre un JSON (de éxito o de error), sin lanzar una excepción ni sacar avisos de PHP. Con una
 * excepción, `PostRequest` recibe un fatal en HTML en vez del envelope de `ContestarJson`, y el
 * frontend lo enseña como «respuesta no JSON».
 *
 * Controladores de lectura: los que no abren una cápsula `HashB` (de eso se encarga
 * `ControladoresConTokenSinTokenTest`) y cuyo nombre no es de una acción que escribe o envía.
 * Fase 5, paso 5.1, del plan de revisión de tests.
 *
 * Además (fase 6): un controlador de lectura **no escribe en la BD**. Cada uno se ejecuta dentro de
 * una transacción que se deshace al acabar ({@see DetectaEscriturasEnBd}), así que el test no cambia
 * datos de desarrollo; si escribe en una tabla que no está en `ESCRITURAS_ADMITIDAS`, falla. Una
 * pantalla que hace algo solo con abrirse (borrar, aprobar…) tiene que pedirlo con una cápsula
 * `HashB` firmada por la pantalla anterior, no ejecutarlo al leer.
 */
final class ControladoresLecturaSinParametrosTest extends myTest
{
    use UbiscamasEnDlInstalada;
    use DetectaEscriturasEnBd;

    private const RAIZ = __DIR__ . '/../../../../';

    /** Nombres de acción que escriben, envían o descargan: fuera de este test. */
    private const PATRON_MUTACION = '/(update|guardar|eliminar|delete|borrar|nuevo|nueva|crear|importar|exportar|enviar|resincronizar|aplicar|upload|subir|download|descargar|mover|copiar|duplicar|publicar|cambiar|marcar|asignar|quitar|add|del|insert|generar|grabar|recuperar|restablecer|login|session|logout|ejecutar|absorber|renombrar|trasladar|cerrar|abrir|anular|firmar|imprimir|sincro|calcular|actualizar|reset)/i';

    /** controlador relativo a src/ => motivo */
    private const EXCLUIDOS = [
        'notas/infrastructure/ui/http/controllers/comprobar_notas_page_body.inc.php' => 'no es un controlador: lo incluye comprobar_notas_page_data.php',
        'certificados/infrastructure/ui/http/controllers/textos_certificados.php' => 'no es un controlador: plantilla de textos en latín que incluye otro controlador',
    ];

    /**
     * Escrituras conocidas de controladores de lectura: controlador relativo a src/ =>
     * [patrón de las tablas (sin la BD), motivo]. No añadir sin una decisión explícita.
     */
    private const ESCRITURAS_ADMITIDAS = [
        'actividadestudios/infrastructure/ui/http/controllers/matriculas_pendientes_data.php' => ['/\.d_matriculas_activ(_dl)? /', 'el listado borra las matrículas pendientes sin nota de personas que ya no están (decisión de la duda 18 del plan de tests)'],
        'actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php' => ['/\.web_preferencias /', 'guarda la última búsqueda del usuario como preferencia'],
        'notas/infrastructure/ui/http/controllers/informe_stgr_agd_data.php' => ['/\.tmp_\w+ /', 'tablas de trabajo del informe'],
        'notas/infrastructure/ui/http/controllers/informe_stgr_n_data.php' => ['/\.tmp_\w+ /', 'tablas de trabajo del informe'],
        'notas/infrastructure/ui/http/controllers/informe_stgr_profesores_data.php' => ['/\.tmp_\w+ /', 'tablas de trabajo del informe'],
    ];

    /** Controladores que toleran un error de SQL (la transacción del test queda abortada): relativo a src/ => motivo. */
    private const ERROR_SQL_TOLERADO = [
        'certificados/infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php' => 'fuera de una región del stgr la tabla de certificados no existe; lo devuelve como error en el JSON',
    ];

    /** @return iterable<string, array{string}> */
    public static function controladoresProvider(): iterable
    {
        $raiz = realpath(self::RAIZ) . '/';
        foreach (glob($raiz . 'src/*/infrastructure/ui/http/controllers/*.php') ?: [] as $f) {
            $rel = substr($f, strlen($raiz . 'src/'));
            if (isset(self::EXCLUIDOS[$rel])
                || preg_match(self::PATRON_MUTACION, basename($f, '.php'))
                || str_contains((string) file_get_contents($f), 'HashB::open(')) {
                continue;
            }
            yield $rel => [$f];
        }
    }

    #[DataProvider('controladoresProvider')]
    public function test_sin_parametros_responde_json(string $fichero): void
    {
        $postAnterior = $_POST;
        $getAnterior = $_GET;
        $_POST = [];
        $_GET = [];
        $avisos = [];
        set_error_handler(static function (int $nivel, string $mensaje, string $f, int $linea) use (&$avisos): bool {
            if (str_contains($f, '/libs/vendor/')) {
                return false;
            }
            $avisos[] = "$mensaje (" . basename($f) . ":$linea)";
            return true;
        });
        // Los controladores de ubiscamas, contra la dl que tiene el módulo de habitaciones instalado.
        if (str_contains($fichero, '/src/ubiscamas/')) {
            $this->usarUbiscamasDeDlInstalada();
        }
        InProcessSrcDispatch::begin();
        ob_start();
        $excepcion = null;
        $bd = ['escrituras' => [], 'abortadas' => []];
        try {
            $bd = $this->escriturasDe(static function () use ($fichero): void {
                require $fichero;
            });
        } catch (\Throwable $e) {
            $excepcion = $e;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            restore_error_handler();
            $this->dejarUbiscamasComoEstaba();
            $_POST = $postAnterior;
            $_GET = $getAnterior;
        }
        $this->assertNull($excepcion, $excepcion === null ? '' : get_class($excepcion) . ': ' . $excepcion->getMessage() . ' (' . basename($excepcion->getFile()) . ':' . $excepcion->getLine() . ')');
        $this->assertSame([], $avisos, 'avisos de PHP');
        $this->assertIsArray(json_decode($salida, true), 'debe responder JSON; respondió: «' . substr($salida, 0, 200) . '»');
        $rel = substr($fichero, strlen((string) realpath(self::RAIZ) . '/src/'));
        $patron = self::ESCRITURAS_ADMITIDAS[$rel][0] ?? null;
        $noAdmitidas = array_values(array_filter(
            $bd['escrituras'],
            static fn (string $tabla): bool => $patron === null || preg_match($patron, $tabla) !== 1
        ));
        $this->assertSame([], $noAdmitidas, 'un controlador de lectura no escribe en la BD (tabla: insertadas/modificadas/borradas)');
        if (!isset(self::ERROR_SQL_TOLERADO[$rel])) {
            $this->assertSame([], $bd['abortadas'], 'error de SQL en estas conexiones');
        }
    }
}
