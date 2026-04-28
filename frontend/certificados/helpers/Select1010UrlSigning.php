<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Firma la URL del botón "adjuntar nuevo certificado" para {@see \src\certificados\domain\Select1010}.
 */
final class Select1010UrlSigning
{
    /**
     * @param array{url_nuevo_spec?: array{path?: string, query?: array<string, mixed>}} $in
     * @return array{url_nuevo: string}
     */
    public static function sign(array $in): array
    {
        $urlNuevo = '';
        if (!empty($in['url_nuevo_spec']) && is_array($in['url_nuevo_spec'])) {
            $spec = $in['url_nuevo_spec'];
            $path = (string)($spec['path'] ?? '');
            $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if ($path !== '') {
                $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
                $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);
                $urlNuevo = HashFront::link($url);
            }
        }

        return ['url_nuevo' => $urlNuevo];
    }
}
