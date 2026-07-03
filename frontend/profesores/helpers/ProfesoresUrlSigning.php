<?php

declare(strict_types=1);

namespace frontend\profesores\helpers;

use frontend\shared\security\HashFrontSignedLink;

final class ProfesoresUrlSigning
{
    /**
     * @return array{path: string, query?: array<string, mixed>}|null
     */
    public static function linkSpecFromMixed(mixed $raw): ?array
    {
        if (!is_array($raw)) {
            return null;
        }
        $path = $raw['path'] ?? null;
        if (!is_string($path) || $path === '') {
            return null;
        }
        $spec = ['path' => $path];
        $query = $raw['query'] ?? null;
        if (is_array($query)) {
            $q = [];
            foreach ($query as $key => $value) {
                $q[(string) $key] = $value;
            }
            $spec['query'] = $q;
        }

        return $spec;
    }

    /**
     * @return array<int|string, mixed>
     */
    public static function goCosasLinkSpecs(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        return $raw;
    }

    /**
     * @param array<int|string, mixed> $goCosasLinkSpecs
     * @return array<string, string>
     */
    public static function goCosasFromSpecs(mixed $fichaSelfLinkSpec, array $goCosasLinkSpecs): array
    {
        $goTo = HashFrontSignedLink::tryFromSpec($fichaSelfLinkSpec);
        $goCosas = [];
        foreach ($goCosasLinkSpecs as $key => $spec) {
            if (!is_string($key) || !is_array($spec)) {
                continue;
            }
            if ($key === 'print') {
                $goCosas[$key] = HashFrontSignedLink::tryFromSpec($spec);
                continue;
            }
            $parsed = self::linkSpecFromMixed($spec);
            if ($parsed === null) {
                continue;
            }
            $query = $parsed['query'] ?? [];
            $query['go_to'] = $goTo;
            $parsed['query'] = $query;
            $goCosas[$key] = HashFrontSignedLink::fromSpec($parsed);
        }

        return $goCosas;
    }
}
