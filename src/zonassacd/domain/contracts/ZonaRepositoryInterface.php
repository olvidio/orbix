<?php

namespace src\zonassacd\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\zonassacd\domain\entity\Zona;

interface ZonaRepositoryInterface
{
    public function isJefeZona(int $id_nom): bool;

    /**
     * @return array<int|string, string>
     */
    public function getArrayZonas(?int $iid_nom_jefe = null): array;

    /**
     * Zonas que cumplen el criterio.
     *
     * @return list<Zona>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Zona>
     */
    public function getZonas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Zona $Zona): bool;

    public function Guardar(Zona $Zona): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_zona): array|false;

    public function findById(int $id_zona): ?Zona;

    public function getNewId(): int;
}
