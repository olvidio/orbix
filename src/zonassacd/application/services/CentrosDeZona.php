<?php

declare(strict_types=1);

namespace src\zonassacd\application\services;

use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

/**
 * Resuelve qué centros pertenecen a una zona (o no tienen ninguna)
 * a partir de `zonas_ctr`, sin mirar la columna `id_zona` de los centros.
 */
final class CentrosDeZona
{
    public function __construct(
        private readonly ZonaCtrRepositoryInterface $zonaCtrRepository,
    ) {
    }

    /**
     * @return list<int>
     */
    public function idUbisDeZona(int $id_zona): array
    {
        if ($id_zona <= 0) {
            return [];
        }

        return $this->zonaCtrRepository->idUbisDeZona($id_zona);
    }

    /**
     * De una lista de candidatos, los que no tienen fila en `zonas_ctr`.
     *
     * @param list<int> $candidatos
     * @return list<int>
     */
    public function idUbisSinZonaEntre(array $candidatos): array
    {
        if ($candidatos === []) {
            return [];
        }
        $mapa = $this->zonaCtrRepository->mapaZonaPorCentro($candidatos);
        $sin = [];
        foreach ($candidatos as $id) {
            if (!isset($mapa[$id])) {
                $sin[] = $id;
            }
        }

        return $sin;
    }

    /**
     * Filtro para `getCentros`: activos cuyo `id_ubi` está en la lista.
     * `null` si no hay ids (el caller no debe lanzar `IN ()`).
     *
     * @param list<int> $idUbis
     * @return array{0: array<string, mixed>, 1: array<string, string>}|null
     */
    public static function whereActivosIn(array $idUbis): ?array
    {
        if ($idUbis === []) {
            return null;
        }

        return [
            ['active' => 't', 'id_ubi' => $idUbis, '_ordre' => 'nombre_ubi'],
            ['id_ubi' => 'IN'],
        ];
    }
}
