<?php

declare(strict_types=1);

namespace frontend\notas\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Firma la URL "nueva nota" para {@see \frontend\notas\helpers\SelectNotasDeUnaPersonaRender}.
 */
final class SelectNotasDeUnaPersonaUrlSigning
{
    /**
     * @param array{link_insert_spec?: array{path?: string, query?: array<string, mixed>}|null} $in
     * @return array{link_insert: string}
     */
    public static function sign(array $in): array
    {
        $linkInsert = '';
        $spec = $in['link_insert_spec'] ?? null;
        if (is_array($spec)) {
            $path = (string)($spec['path'] ?? '');
            $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
            if ($path !== '') {
                $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
                $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);
                $linkInsert = HashFront::link($url);
            }
        }

        return ['link_insert' => $linkInsert];
    }
}
