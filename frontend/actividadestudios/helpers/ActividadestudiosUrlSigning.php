<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\security\HashFrontSignedLink;

/**
 * Firma de enlaces a partir de `link_spec` en pantallas actividadestudios.
 */
final class ActividadestudiosUrlSigning
{
    public static function dossierLink(mixed $spec): string
    {
        $parsed = ActividadestudiosRenderSupport::linkSpec($spec);

        return $parsed !== null ? DossierTipoFormLinkSpecsSigning::fromSpec($parsed) : '';
    }

    public static function signedLink(mixed $spec): string
    {
        $parsed = ActividadestudiosRenderSupport::linkSpec($spec);
        if ($parsed === null) {
            return '';
        }

        return HashFrontSignedLink::fromSpec($parsed);
    }
}
