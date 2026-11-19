<?php

declare(strict_types=1);

// Script de línea de órdenes: no hace nada si se pide por el servidor web.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

/**
 * Funciones puras para montar `frontend/menus/view/traducir_menu.phtml`: un fichero PHP que solo
 * contiene `_("…")` con los textos que la aplicación traduce en tiempo de ejecución (`_($menu)`,
 * `_($nombreGrupo)`, …) y que xgettext no puede ver en el código.
 *
 * Las usa `tools/i18n/generar_textos_menus.php`. Test: `tests/unit/tools/i18n/TextosMenusLibTest.php`.
 */

/**
 * Literal PHP entre comillas dobles cuyo valor es exactamente `$texto`
 * (escapa `\`, `"` y `$`, como pide la guía de gettext).
 */
function i18n_literal_php(string $texto): string
{
    $escapado = strtr($texto, [
        '\\' => '\\\\',
        '"' => '\\"',
        '$' => '\\$',
        "\n" => '\\n',
        "\r" => '\\r',
        "\t" => '\\t',
    ]);

    return '"' . $escapado . '"';
}

/**
 * Contenido del fichero de textos. Cada sección (título => textos) sale ordenada, sin vacíos ni
 * duplicados; un texto que ya salió en una sección anterior no se repite.
 *
 * El texto se conserva tal cual (también espacios al principio o al final): `_($menu)` busca el
 * msgid exacto.
 *
 * @param array<string, list<string>> $secciones
 */
function i18n_phtml_textos(array $secciones): string
{
    $out = "<?php\n"
        . "// Generado por: php tools/i18n/generar_textos_menus.php (no editar a mano).\n"
        . "// Textos de la BD que la aplicación traduce con _(\$variable); este fichero solo sirve\n"
        . "// para que xgettext los lleve a languages/es_ES.pot.\n";
    $vistos = [];
    foreach ($secciones as $titulo => $textos) {
        $lista = [];
        foreach ($textos as $texto) {
            if (trim($texto) === '' || isset($vistos[$texto])) {
                continue;
            }
            $vistos[$texto] = true;
            $lista[] = $texto;
        }
        usort($lista, static fn (string $a, string $b): int => strcmp($a, $b));
        $out .= "\n// " . $titulo . "\n";
        foreach ($lista as $texto) {
            $out .= '_(' . i18n_literal_php($texto) . ");\n";
        }
    }

    return $out;
}
