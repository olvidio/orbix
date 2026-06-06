<?php

namespace src\casas\domain\contracts;

use src\casas\domain\entity\UbiGasto;
use src\shared\domain\value_objects\DateTimeLocal;

interface UbiGastoRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<UbiGasto>
     */
    public function getUbisGastos(array $aWhere = [], array $aOperators = []): array;

    public function getSumaGastos(int $id_ubi, int $tipo, DateTimeLocal $oInicio, DateTimeLocal $oFin): float;

    public function Eliminar(UbiGasto $UbiGasto): bool;

    public function Guardar(UbiGasto $UbiGasto): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?UbiGasto;

    public function getNewId(): int;
}
