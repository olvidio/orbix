<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PHPUnit\Framework\Attributes\DataProvider;
use src\shared\infrastructure\InProcessSrcDispatch;
use Tests\myTest;

/**
 * Test-guardia: un controlador de lectura de `src/` llamado sin parámetros (POST vacío) responde
 * siempre un JSON (de éxito o de error), sin lanzar una excepción ni sacar avisos de PHP. Con una
 * excepción, `PostRequest` recibe un fatal en HTML en vez del envelope de `ContestarJson`, y el
 * frontend lo enseña como «respuesta no JSON».
 *
 * Controladores de lectura: los que no abren una cápsula `HashB` (de eso se encarga
 * `ControladoresConTokenSinTokenTest`) y cuyo nombre no es de una acción que escribe o envía.
 * Fase 5, paso 5.1, del plan de revisión de tests.
 */
final class ControladoresLecturaSinParametrosTest extends myTest
{
    private const RAIZ = __DIR__ . '/../../../../';

    /** Nombres de acción que escriben, envían o descargan: fuera de este test. */
    private const PATRON_MUTACION = '/(update|guardar|eliminar|delete|borrar|nuevo|nueva|crear|importar|exportar|enviar|resincronizar|aplicar|upload|subir|download|descargar|mover|copiar|duplicar|publicar|cambiar|marcar|asignar|quitar|add|del|insert|generar|grabar|recuperar|restablecer|login|session|logout|ejecutar|absorber|renombrar|trasladar|cerrar|abrir|anular|firmar|imprimir|sincro|calcular|actualizar|reset)/i';

    /** controlador relativo a src/ => motivo */
    private const EXCLUIDOS = [
        'notas/infrastructure/ui/http/controllers/comprobar_notas_page_body.inc.php' => 'no es un controlador: lo incluye comprobar_notas_page_data.php',
        'certificados/infrastructure/ui/http/controllers/textos_certificados.php' => 'no es un controlador: plantilla de textos en latín que incluye otro controlador',
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
        InProcessSrcDispatch::begin();
        ob_start();
        $excepcion = null;
        try {
            require $fichero;
        } catch (\Throwable $e) {
            $excepcion = $e;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
            restore_error_handler();
            $_POST = $postAnterior;
            $_GET = $getAnterior;
        }
        $this->assertNull($excepcion, $excepcion === null ? '' : get_class($excepcion) . ': ' . $excepcion->getMessage() . ' (' . basename($excepcion->getFile()) . ':' . $excepcion->getLine() . ')');
        $this->assertSame([], $avisos, 'avisos de PHP');
        $this->assertIsArray(json_decode($salida, true), 'debe responder JSON; respondió: «' . substr($salida, 0, 200) . '»');
    }
}
