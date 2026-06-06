<?php

namespace src\actividadtarifas\domain\contracts;

use src\actividadtarifas\domain\entity\TipoTarifa;

interface TipoTarifaRepositoryInterface
{
    /**
     * @return array<int, string>
     */
    public function getArrayTipoTarifas(int|string $isfsv = ''): array;

    /**
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
