<?php

declare(strict_types=1);

namespace frontend\shared\security;

use frontend\shared\config\AppUrlConfig;

/**
 * Firma link_specs (`{path, query?}`) a URLs firmadas con HashFront en el borde del frontend.
 *
 * Los casos de uso del backend devuelven datos planos (specs) y el frontend es el único
 * responsable de firmar: nada de HashFront en `src/`.
 */
final class HashFrontSignedLink
{
    public static function tryFromSpec(mixed $value): string
    {
        $spec = self::parseLinkSpec($value);

        return $spec === null ? '' : self::fromSpec($spec);
    }

    /**
     * @param array{path: string, query?: array<string, mixed>} $spec
     */
    public static function fromSpec(array $spec): string
    {
        $path = $spec['path'];
        if ($path === '') {
            return '';
        }
        $query = $spec['query'] ?? [];
        $url = AppUrlConfig::browserUrlFromAppRelative($path);
        if ($url === '') {
            return '';
        }
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return HashFront::link($url);
    }

    /**
     * Firma un mapa `etiqueta => spec` y devuelve `etiqueta => url_firmada`.
     *
     * @param array<string, array{path: string, query?: array<string, mixed>}> $specsByLabel
     * @return array<string, string>
     */
    public static function fromSpecMap(array $specsByLabel): array
    {
        $out = [];
        foreach ($specsByLabel as $label => $spec) {
            $out[$label] = self::fromSpec($spec);
        }

        return $out;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function parseQuery(mixed $query): ?array
    {
        if (!is_array($query)) {
            return null;
        }
        $out = [];
        foreach ($query as $key => $val) {
            $out[(string) $key] = $val;
        }

        return $out;
    }

    /**
     * @return array{path: string, query?: array<string, mixed>}|null
     */
    private static function parseLinkSpec(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }
        $path = $value['path'] ?? null;
        if (!is_string($path) || $path === '') {
            return null;
        }
        $query = $value['query'] ?? null;
        if ($query === null) {
            return ['path' => $path];
        }
        $normalizedQuery = self::parseQuery($query);
        if ($normalizedQuery === null) {
            return null;
        }

        return ['path' => $path, 'query' => $normalizedQuery];
    }

    /**
     * En un listado de filas, convierte cada `<col>_link_spec` en `<col>` firmado.
     *
     * @param list<array<string, mixed>> $rows
     * @param list<string>               $cols columnas cuyos `<col>_link_spec` hay que firmar
     *
     * @return list<array<string, mixed>>
     */
    public static function signRowLinkSpecs(array $rows, array $cols): array
    {
        foreach ($rows as $i => $row) {
            foreach ($cols as $col) {
                $specKey = $col . '_link_spec';
                $spec = self::parseLinkSpec($row[$specKey] ?? null);
                if ($spec === null) {
                    continue;
                }
                $rows[$i][$col] = self::fromSpec($spec);
                unset($rows[$i][$specKey]);
            }
        }

        return $rows;
    }
}
