<?php

declare(strict_types=1);

namespace Tests\unit\tools\i18n;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/tools/i18n/textos_menus_lib.php';

final class TextosMenusLibTest extends TestCase
{
    public function test_literal_escapa_comilla_dolar_y_barra(): void
    {
        self::assertSame('"di \"hola\" a \$x y a c:\\\\tmp"', i18n_literal_php('di "hola" a $x y a c:\\tmp'));
        // El literal generado vale exactamente el texto original.
        $texto = 'di "hola" a $x y a c:\\tmp';
        self::assertSame($texto, eval('return ' . i18n_literal_php($texto) . ';'));
    }

    public function test_literal_conserva_apostrofos_y_espacios(): void
    {
        self::assertSame('" Nuevo plan"', i18n_literal_php(' Nuevo plan'));
        self::assertSame("\"l'acta\"", i18n_literal_php("l'acta"));
    }

    public function test_fichero_por_secciones_ordenado_sin_duplicados_ni_vacios(): void
    {
        $php = i18n_phtml_textos([
            'grupos de menú' => ['vsm', 'Utilidades', 'vsm'],
            'menús' => ['preferencias', '', '   ', 'Actas', 'preferencias'],
        ]);

        self::assertStringStartsWith("<?php\n", $php);
        self::assertStringContainsString("// grupos de menú\n_(\"Utilidades\");\n_(\"vsm\");\n", $php);
        self::assertStringContainsString("// menús\n_(\"Actas\");\n_(\"preferencias\");\n", $php);
        self::assertSame(1, substr_count($php, '_("preferencias")'));
        self::assertStringNotContainsString('_("")', $php);
        self::assertStringNotContainsString('_("   ")', $php);
    }

    public function test_un_texto_en_dos_secciones_sale_solo_en_la_primera(): void
    {
        $php = i18n_phtml_textos([
            'grupos de menú' => ['Utilidades'],
            'menús' => ['Utilidades', 'exportar'],
        ]);

        self::assertSame(1, substr_count($php, '_("Utilidades")'));
        self::assertStringContainsString("// menús\n_(\"exportar\");\n", $php);
    }

    public function test_el_fichero_generado_es_php_valido_y_xgettext_lo_lee(): void
    {
        $php = i18n_phtml_textos(['menús' => ['di "hola"', 'precio $5', "l'acta"]]);
        $tmp = tempnam(sys_get_temp_dir(), 'i18n') . '.php';
        file_put_contents($tmp, $php);
        try {
            exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $out, $code);
            self::assertSame(0, $code, implode("\n", $out));
            if (trim((string)shell_exec('command -v xgettext')) !== '') {
                $pot = (string)shell_exec('xgettext --language=PHP --from-code=UTF-8 -k_ -o - ' . escapeshellarg($tmp));
                self::assertStringContainsString('msgid "di \"hola\""', $pot);
                self::assertStringContainsString('msgid "precio $5"', $pot);
                self::assertStringContainsString("msgid \"l'acta\"", $pot);
            }
        } finally {
            @unlink($tmp);
        }
    }
}
