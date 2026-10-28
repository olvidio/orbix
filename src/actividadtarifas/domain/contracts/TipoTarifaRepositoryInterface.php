<?php

namespace src\actividadtarifas\domain\contracts;

use src\actividadtarifas\domain\entity\TipoTarifa;
use src\shared\domain\criteria\Criteria;

interface TipoTarifaRepositoryInterface
{
    /**
     * @return array<int, string>
     */
    public function getArrayTipoTarifas(int|string $isfsv = ''): array;

    /**
     * TipoTarifa que cumplen el criterio.
     *
     * @return list<TipoTarifa>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<TipoTarifa>
     */
    public function getTipoTarifas(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(TipoTarifa $TipoTarifa): bool;

    public function Guardar(TipoTarifa $TipoTarifa): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tarifa): array|false;

    public function findById(int $id_tarifa): ?TipoTarifa;

    public function getNewId(): int;
}
