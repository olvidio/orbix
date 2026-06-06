<?php

namespace src\casas\domain\contracts;

use src\casas\domain\entity\Ingreso;

interface IngresoRepositoryInterface
{
    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Ingreso>
     */
    public function getIngresos(array $aWhere = [], array $aOperators = []): array;

    public function Eliminar(Ingreso $Ingreso): bool;

    public function Guardar(Ingreso $Ingreso): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_activ): array|false;

    public function findById(int $id_activ): ?Ingreso;
}
