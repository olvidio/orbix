<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use PHPUnit\Framework\TestCase;

/**
 * Test-guardia: ningún mensaje que puede ver el usuario lleva la ruta de un fichero del servidor.
 *
 * Se detecta `__FILE__` concatenado (`. __FILE__`, `__FILE__ .`) o pasado a un `sprintf(_(...))`
 * sin `basename()`. Como argumento de las llamadas al log (`reportPdoError(..., __FILE__)`,
 * `prepareAndExecute(..., __FILE__, ...)`) sigue permitido: no llega al usuario.
 *
 * Para identificar una rama imposible basta `basename(__FILE__)`.
 */
final class MensajesSinRutaDelServidorTest extends TestCase
{
    public function test_ningun_mensaje_lleva_la_ruta_de_un_fichero(): void
    {
        $raiz = dirname(__DIR__, 4);
        $encontrados = [];
        foreach (['src', 'frontend', 'public'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . '/' . $dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $fichero) {
                /** @var \SplFileInfo $fichero */
                if (!preg_match('/\.(php|inc|phtml)$/', $fichero->getFilename())) {
                    continue;
                }
                $lineas = file($fichero->getPathname()) ?: [];
                foreach ($lineas as $n => $linea) {
                    $codigo = ltrim($linea);
                    if (str_starts_with($codigo, '//') || str_starts_with($codigo, '*') || str_starts_with($codigo, '#')) {
                        continue;
                    }
                    $concatenado = preg_match('/\.\s*__FILE__|__FILE__\s*\./', $linea) === 1;
                    $enMensaje = preg_match('/sprintf\(\s*_\(.*(?<!basename\()__FILE__/', $linea) === 1;
                    if ($concatenado || $enMensaje) {
                        $encontrados[] = substr($fichero->getPathname(), strlen($raiz) + 1) . ':' . ($n + 1);
                    }
                }
            }
        }
        sort($encontrados);

        $this->assertSame([], $encontrados, "Mensajes con la ruta del fichero (usar basename(__FILE__) o quitarla):\n" . implode("\n", $encontrados));
    }
}
