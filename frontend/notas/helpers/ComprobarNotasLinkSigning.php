<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\security\HashFSignedLink;

/**
 * Firma en frontend los enlaces de acción devueltos como `link_specs` por comprobar notas.
 */
final class ComprobarNotasLinkSigning
{
    /**
     * @param array<string, mixed> $linkSpecs
     */
    public static function signHtml(string $html, array $linkSpecs): string
    {
        foreach ($linkSpecs as $token => $spec) {
            if (!is_string($token) || $token === '') {
                continue;
            }
            $html = str_replace($token, HashFSignedLink::tryFromSpec($spec), $html);
        }

        return $html;
    }
}
