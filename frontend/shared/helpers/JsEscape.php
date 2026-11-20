<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Valores que PHP escribe dentro de un bloque `<script>`.
 *
 * `json()` da el literal completo (`var x = <?= JsEscape::json($dato) ?>;`). `cadena()` da solo el
 * contenido, para cuando el valor va dentro de una cadena que ya está escrita en la vista
 * (`'region=<?= JsEscape::cadena($region) ?>&dl=' + dl`). En los dos casos el valor que recibe el
 * JavaScript es el mismo, y ni las comillas ni `</script>` ni `<!--` salen tal cual.
 */
final class JsEscape
{
    private const FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE;

    public static function json(mixed $valor): string
    {
        $json = json_encode($valor, self::FLAGS);

        return $json === false ? 'null' : $json;
    }

    public static function cadena(mixed $valor): string
    {
        $texto = is_scalar($valor) ? (string) $valor : '';

        return substr(self::json($texto), 1, -1);
    }
}
