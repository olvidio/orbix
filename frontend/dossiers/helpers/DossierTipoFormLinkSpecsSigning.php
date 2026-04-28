<?php

declare(strict_types=1);

namespace frontend\dossiers\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Firma URLs de formulario dossier (`DossierTipoPublicUrls::formControllerLinkSpec`) en el borde
 * donde se renderiza la vista (widgets Select_* desde `getHtml()`).
 */
final class DossierTipoFormLinkSpecsSigning
{
    /**
     * @param array<string, array{path: string, query?: array<string, mixed>}> $specsByLabel
     *
     * @return array<string, string>
     */
    public static function signLinkMap(array $specsByLabel): array
    {
        $out = [];
        foreach ($specsByLabel as $label => $spec) {
            if (!is_array($spec)) {
                continue;
            }
            $out[$label] = self::fromSpec($spec);
        }

        return $out;
    }

    /**
     * @param array{path: string, query?: array<string, mixed>} $spec
     */
    public static function fromSpec(array $spec): string
    {
        $path = (string)($spec['path'] ?? '');
        $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
        if ($path === '') {
            return '';
        }
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);

        return HashFront::link($url);
    }
}
