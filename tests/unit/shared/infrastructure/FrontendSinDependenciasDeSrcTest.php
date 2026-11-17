<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use Tests\myTest;

/**
 * Test-guardia: los módulos de `frontend/` no usan clases de `src\` (ni con `use` ni con el nombre
 * completo); piden los datos a `/src/...` con `PostRequest`. Los nombres dentro de cadenas o
 * comentarios no cuentan.
 *
 * `frontend/shared/` es el puente con `src/`: arranque (`FrontBootstrap`), despacho en proceso
 * (`PostRequest`), sesión, configuración (`OrbixRuntime`, `AppUrlConfig`) y fachadas como
 * `frontend\shared\helpers\FuncTablasSupport`. Las excepciones fuera de él van abajo, con su motivo.
 */
final class FrontendSinDependenciasDeSrcTest extends myTest
{
    /** fichero relativo a frontend/ => motivo */
    private const EXCEPCIONES = [
        'devel_codegen/controller/factory.php' => 'generador de código de desarrollo: lee la estructura de la BD con GlobalPdo; pendiente de decidir si pasa a tools/',
    ];

    public function test_los_modulos_de_frontend_no_usan_clases_de_src(): void
    {
        $raiz = dirname(__DIR__, 4) . '/frontend/';
        $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz, \FilesystemIterator::SKIP_DOTS));
        $con = [];
        foreach ($iter as $f) {
            if (!$f instanceof \SplFileInfo || !in_array($f->getExtension(), ['php', 'phtml', 'inc'], true)) {
                continue;
            }
            $rel = substr($f->getPathname(), strlen($raiz));
            if (str_starts_with($rel, 'shared/') || isset(self::EXCEPCIONES[$rel])) {
                continue;
            }
            foreach (\PhpToken::tokenize((string) file_get_contents($f->getPathname())) as $t) {
                $nombre = ltrim($t->text, '\\');
                if (in_array($t->id, [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true) && str_starts_with($nombre, 'src\\')) {
                    $con[] = "frontend/$rel:{$t->line} {$t->text}";
                }
            }
        }

        $this->assertSame([], $con, "Clases de src\\ usadas desde módulos de frontend/:\n" . implode("\n", $con));
    }

    public function test_las_excepciones_siguen_existiendo(): void
    {
        foreach (array_keys(self::EXCEPCIONES) as $rel) {
            $this->assertFileExists(dirname(__DIR__, 4) . '/frontend/' . $rel, 'excepción de un fichero que ya no existe: quitarla de la lista');
        }
    }
}
