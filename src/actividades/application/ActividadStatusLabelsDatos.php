<?php

namespace src\actividades\application;

use src\actividades\domain\value_objects\StatusId;

/**
 * Etiquetas traducidas de status de actividad (formulario ver/editar/nuevo).
 */
final class ActividadStatusLabelsDatos
{
    /**
     * @return array{id_to_label: array<int|string, string>}
     */
    public function execute(bool $withAll): array
    {
        return ['id_to_label' => StatusId::getArrayStatus($withAll)];
    }
}
