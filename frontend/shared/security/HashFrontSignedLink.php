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
    /**
     * @param array{path: string, query?: array<string, mixed>} $spec
     */
    public static function fromSpec(array $spec): string
    {
        $path = (string) ($spec['path'] ?? '');
        if ($path === '') {
            return '';
        }
        $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $url = $base . '/' . ltrim($path, '/');
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
            if (!is_array($spec)) {
                continue;
            }
            $out[$label] = self::fromSpec($spec);
        }

        return $out;
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
            if (!is_array($row)) {
                continue;
            }
            foreach ($cols as $col) {
                $specKey = $col . '_link_spec';
                if (!empty($row[$specKey]) && is_array($row[$specKey])) {
                    $rows[$i][$col] = self::fromSpec($row[$specKey]);
                    unset($rows[$i][$specKey]);
                }
            }
        }

        return $rows;
    }
}
