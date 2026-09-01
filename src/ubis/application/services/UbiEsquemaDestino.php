<?php

declare(strict_types=1);

namespace src\ubis\application\services;

/**
 * Interpreta región + dl como esquema Orbix (p. ej. H + dlmE → H-dlmE).
 * También acepta que el usuario haya puesto el nombre de esquema en región o en dl.
 */
final class UbiEsquemaDestino
{
    /**
     * @return array{region: string, dl: string, esquema: string}
     */
    public static function normalizar(string $region, string $dl): array
    {
        $region = trim($region);
        $dl = trim($dl);

        if (self::pareceEsquema($dl)) {
            [$region, $dl] = self::partirEsquema($dl);
        } elseif (self::pareceEsquema($region)) {
            $partido = self::partirEsquema($region);
            if ($dl === '' || $dl === $partido[1]) {
                [$region, $dl] = $partido;
            }
        }

        $esquema = ($region !== '' && $dl !== '') ? $region . '-' . $dl : '';

        return [
            'region' => $region,
            'dl' => $dl,
            'esquema' => $esquema,
        ];
    }

    public static function pareceEsquema(string $valor): bool
    {
        $valor = trim($valor);
        if ($valor === '' || !str_contains($valor, '-')) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]*-[A-Za-z0-9][A-Za-z0-9_-]*$/', $valor);
    }

    /**
     * @return array{0: string, 1: string}
     */
    public static function partirEsquema(string $esquema): array
    {
        $parts = explode('-', trim($esquema), 2);

        return [$parts[0] ?? '', $parts[1] ?? ''];
    }

    public static function esIdentificadorValido(string $esquema): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,31}$/', $esquema);
    }
}
