<?php

declare(strict_types=1);

namespace src\zonassacd\application\services;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\CriteriaField;
use src\shared\domain\criteria\Filter;
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
     * Criterio para `matching()` de un repositorio de centros: activos cuyo `id_ubi` está en la lista,
     * ordenados por `nombre_ubi`. `null` si no hay ids (el caller no debe lanzar `IN ()`).
     *
     * Cada repositorio de centros solo acepta su propio enum de campos (`CentroDlField`,
     * `CentroEllasField`, `CentroEllosField`...), todos con los mismos nombres de campo:
     * el llamante indica cuál.
     *
     * @param list<int> $idUbis
     * @param class-string<CriteriaField&\BackedEnum> $campos enum de campos del repositorio destino
     */
    public static function criterioActivosIn(array $idUbis, string $campos): ?Criteria
    {
        if ($idUbis === []) {
            return null;
        }

        return Criteria::create()
            ->where(Filter::eq($campos::from('active'), true))
            ->andWhere(Filter::in($campos::from('id_ubi'), $idUbis))
            ->orderBy($campos::from('nombre_ubi'));
    }
}
