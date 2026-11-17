<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure;

use Tests\myTest;

/**
 * Test-guardia: ningún fichero PHP de `src/` usa clases de `frontend\` (ni con `use`
 * ni con el nombre completo). Lo que `src/` necesite vive en `src/shared`; el frontend
 * llama a `src/` por `PostRequest`, nunca al revés. Los `{@see \frontend\...}` de los
 * comentarios no cuentan.
 */
final class SrcSinDependenciasDeFrontendTest extends myTest
{
    public function test_src_no_usa_clases_de_frontend(): void
    {
        $raiz = dirname(__DIR__, 4) . '/';
        $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($raiz . 'src', \FilesystemIterator::SKIP_DOTS));
        $con = [];
        foreach ($iter as $f) {
            if (!$f instanceof \SplFileInfo || !in_array($f->getExtension(), ['php', 'phtml', 'inc'], true)) {
                continue;
            }
            $rel = substr($f->getPathname(), strlen($raiz));
            foreach (\PhpToken::tokenize((string) file_get_contents($f->getPathname())) as $t) {
                $nombre = ltrim($t->text, '\\');
                if (in_array($t->id, [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true) && str_starts_with($nombre, 'frontend\\')) {
                    $con[] = "$rel:{$t->line} {$t->text}";
                }
            }
        }

        $this->assertSame([], $con, "Clases de frontend\\ usadas desde src/:\n" . implode("\n", $con));
    }
}
