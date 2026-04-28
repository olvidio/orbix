<?php

namespace src\dossiers\infrastructure\ui\http;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

/**
 * Firma URLs hacia controladores frontend (borde HTTP / infraestructura).
 */
final class SignPublicFrontendLink
{
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
        $base = AppUrlConfig::getPublicAppBaseUrl();
        $url = $base . '/' . ltrim($path, '/') . '?' . http_build_query($query);

        return HashFront::link($url);
    }

    /**
     * @param list<array<string, mixed>> $a_filas
     * @return list<array<string, mixed>>
     */
    public static function resolveDossiersListaFichasFilas(array $a_filas): array
    {
        foreach ($a_filas as $i => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            foreach (['href_ver', 'href_abrir'] as $col) {
                $sk = $col . '_link_spec';
                if (!empty($fila[$sk]) && is_array($fila[$sk])) {
                    $a_filas[$i][$col] = self::fromSpec($fila[$sk]);
                    unset($a_filas[$i][$sk]);
                }
            }
        }

        return $a_filas;
    }
}
