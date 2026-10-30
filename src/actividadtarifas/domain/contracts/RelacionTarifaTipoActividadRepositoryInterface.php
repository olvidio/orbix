<?php

namespace src\actividadtarifas\domain\contracts;

use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\shared\domain\criteria\Criteria;

interface RelacionTarifaTipoActividadRepositoryInterface
{
    /**
     * RelacionTarifaTipoActividad que cumplen el criterio.
     *
     * @return list<RelacionTarifaTipoActividad>
     */
    public function matching(Criteria $criteria): array;

    public function Eliminar(RelacionTarifaTipoActividad $RelacionTarifaTipoActividad): bool;

    public function Guardar(RelacionTarifaTipoActividad $RelacionTarifaTipoActividad): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false;

    public function findById(int $id_item): ?RelacionTarifaTipoActividad;

    public function getNewId(): int;
}
