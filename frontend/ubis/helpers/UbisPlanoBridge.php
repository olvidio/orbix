<?php

declare(strict_types=1);

namespace frontend\ubis\helpers;

/**
 * Bridge FQCN a repos Pg* de planos; evita `use src\...` en {@see UbisPayload}.
 */
final class UbisPlanoBridge
{
    /**
     * @return array<string, mixed>|null
     */
    public static function planoDownload(string $tipo, int $id_direccion): ?array
    {
        return match ($tipo) {
            'DireccionCentro' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroRepository())->planoDownload($id_direccion),
            'DireccionCentroDl' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroDlRepository())->planoDownload($id_direccion),
            'DireccionCentroEx' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroExRepository())->planoDownload($id_direccion),
            'DireccionCdc' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaRepository())->planoDownload($id_direccion),
            'DireccionCdcDl' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaDlRepository())->planoDownload($id_direccion),
            'DireccionCdcEx' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaExRepository())->planoDownload($id_direccion),
            default => null,
        };
    }

    /**
     * @param resource|string|null $payload
     * @return mixed
     */
    public static function planoUpload(string $tipo, int $id_direccion, string $nom, string $extension, mixed $payload): mixed
    {
        return match ($tipo) {
            'DireccionCentro' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
            'DireccionCentroDl' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroDlRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
            'DireccionCentroEx' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroExRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
            'DireccionCdc' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
            'DireccionCdcDl' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaDlRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
            'DireccionCdcEx' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaExRepository())->planoUpload($id_direccion, $nom, $extension, $payload),
            default => null,
        };
    }

    public static function planoBorrar(string $tipo, int $id_direccion): mixed
    {
        return match ($tipo) {
            'DireccionCentro' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroRepository())->planoBorrar($id_direccion),
            'DireccionCentroDl' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroDlRepository())->planoBorrar($id_direccion),
            'DireccionCentroEx' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroExRepository())->planoBorrar($id_direccion),
            'DireccionCdc' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaRepository())->planoBorrar($id_direccion),
            'DireccionCdcDl' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaDlRepository())->planoBorrar($id_direccion),
            'DireccionCdcEx' => (new \src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaExRepository())->planoBorrar($id_direccion),
            default => null,
        };
    }
}
