<?php

namespace src\casas\domain\contracts;

use src\casas\domain\entity\Ingreso;
use src\shared\domain\criteria\Criteria;

interface IngresoRepositoryInterface
{
    /**
     * Ingresos de actividad que cumplen el criterio.
     *
     * @return list<Ingreso>
     */
    public function matching(Criteria $criteria): array;


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
