<?php

declare(strict_types=1);

namespace Tests\unit\frontend\vistas;

use PHPUnit\Framework\TestCase;

/**
 * Un `<?= ... ?>` dentro de un atributo entrecomillado de una vista `.phtml` va escapado: un dato con una
 * comilla cierra el atributo y lo que sigue se interpreta como HTML (revisión de seguridad del 2026-11-20,
 * XSS-04). Sirve `htmlspecialchars(...)`, `json_encode(...)`, un entero, un texto fijo de gettext o uno de
 * los ayudantes de escapado de la propia vista (`$e(...)`, `$js(...)`).
 */
final class VistasEscapanAtributosTest extends TestCase
{
    private const SEGURO = '~^(htmlspecialchars\(|json_encode\(|\((int|float)\)|round\(|_\(|ucfirst\(_\(|ucwords\(_\(|strtoupper\(_\(|sprintf\(_\('
        . '|\$(e|js|onclick)\(|\$\w+_esc$|\$\w+->(getCamposHtml|desplegable|mostrarNavAtras\w*|jsNavAtras\w*)\(|OrbixRuntime::|AppUrlConfig::)'
        . '|\? *\'[ a-z]*\' *: *\'[ a-z-]*\'$~';

    public function test_ningun_atributo_pinta_un_dato_sin_escapar(): void
    {
        $raiz = dirname(__DIR__, 4);
        $sinEscapar = [];
        foreach ([$raiz . '/frontend', $raiz . '/public'] as $dir) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $fichero) {
                if (!str_ends_with((string) $fichero, '.phtml')) {
                    continue;
                }
                foreach ($this->salidasEnAtributos((string) file_get_contents((string) $fichero)) as [$linea, $expr]) {
                    if (preg_match(self::SEGURO, $expr) !== 1) {
                        $sinEscapar[] = substr((string) $fichero, strlen($raiz) + 1) . ':' . $linea . '  ' . $expr;
                    }
                }
            }
        }

        $this->assertSame([], $sinEscapar, "Salidas sin escapar dentro de un atributo:\n" . implode("\n", $sinEscapar));
    }

    /** @return list<array{int, string}> */
    private function salidasEnAtributos(string $codigo): array
    {
        $salidas = [];
        $html = '';
        $tokens = token_get_all($codigo);
        $n = count($tokens);
        for ($i = 0; $i < $n; $i++) {
            $t = $tokens[$i];
            if (is_array($t) && $t[0] === T_INLINE_HTML) {
                $html .= $t[1];
                continue;
            }
            if (!is_array($t) || $t[0] !== T_OPEN_TAG_WITH_ECHO) {
                continue;
            }
            $linea = $t[2];
            $expr = '';
            for ($i++; $i < $n; $i++) {
                $u = $tokens[$i];
                if (is_array($u) && $u[0] === T_CLOSE_TAG) {
                    break;
                }
                $expr .= is_array($u) ? $u[1] : $u;
            }
            if ($this->dentroDeUnAtributo($html)) {
                $salidas[] = [$linea, (string) preg_replace('~\s+~', ' ', trim(rtrim(trim($expr), ';')))];
            }
        }

        return $salidas;
    }

    private function dentroDeUnAtributo(string $html): bool
    {
        $abreScript = strripos($html, '<script');
        $cierraScript = strripos($html, '</script');
        if ($abreScript !== false && ($cierraScript === false || $cierraScript < $abreScript)) {
            return false;
        }
        $abre = strrpos($html, '<');
        $cierra = strrpos($html, '>');
        if ($abre === false || ($cierra !== false && $cierra > $abre)) {
            return false;
        }
        $etiqueta = substr($html, $abre);
        if (substr_count($etiqueta, '"') % 2 === 1) {
            return true;
        }

        return substr_count((string) preg_replace('~"[^"]*"~', '', $etiqueta), "'") % 2 === 1;
    }
}
