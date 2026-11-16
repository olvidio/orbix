<?php

declare(strict_types=1);

namespace Tests\unit\frontend;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Dentro de un bloque <script> el navegador no decodifica entidades HTML: un valor
 * pasado con htmlspecialchars() llega al JavaScript tal cual, con `&amp;`, `&#039;`…
 *
 * Caso real: lista_habitaciones.phtml abría con window.open('…?id_activ=1&amp;h=…');
 * PHP recibía `amp;h` en lugar de `h`, HashF no validaba nada y la página salía
 * como «página con seguridad desactivada».
 *
 * En <script> se usa json_encode(..., JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
 * (sin comillas alrededor). htmlspecialchars sigue siendo lo correcto en atributos
 * HTML (onclick="…", href="…"), que el navegador sí decodifica.
 */
class VistasSinHtmlspecialcharsEnScriptTest extends TestCase
{
    public function test_ninguna_vista_usa_htmlspecialchars_dentro_de_script(): void
    {
        $root = dirname(__DIR__, 3);
        $violations = [];
        foreach (['frontend', 'src', 'public'] as $dir) {
            $base = $root . '/' . $dir;
            if (!is_dir($base)) {
                continue;
            }
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if (!preg_match('/\.(phtml|php)$/', $file->getFilename())) {
                    continue;
                }
                $path = $file->getPathname();
                $content = (string)file_get_contents($path);
                foreach (self::usosEnScript($content) as $line) {
                    $violations[] = substr($path, strlen($root) + 1) . ':' . $line;
                }
            }
        }

        self::assertSame(
            [],
            $violations,
            "htmlspecialchars() dentro de <script>: usar json_encode(..., JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) sin comillas alrededor.\n"
            . implode("\n", $violations)
        );
    }

    public function test_detecta_el_caso_de_lista_habitaciones(): void
    {
        $html = "<button onclick=\"f('<?= htmlspecialchars(\$x, ENT_QUOTES) ?>')\">a</button>\n"
            . "<script>\n"
            . "window.open('<?= htmlspecialchars(\$nombres_open_url, ENT_QUOTES) ?>', 'r');\n"
            . "</script>\n";

        self::assertSame([3], self::usosEnScript($html));
    }

    /**
     * @return list<int> líneas con `<?= htmlspecialchars(` dentro de un bloque <script>
     */
    private static function usosEnScript(string $content): array
    {
        if (!preg_match_all('#<script\b[^>]*>(.*?)</script>#si', $content, $blocks, PREG_OFFSET_CAPTURE)) {
            return [];
        }
        $lines = [];
        foreach ($blocks[1] as [$body, $offset]) {
            if (!preg_match_all('/<\?(?:=|php\s+echo)\s*htmlspecialchars\s*\(/', $body, $m, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            foreach ($m[0] as [, $pos]) {
                $lines[] = substr_count($content, "\n", 0, $offset + $pos) + 1;
            }
        }
        return $lines;
    }
}
