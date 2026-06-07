<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla `actividad_proceso`.
 */
class ActividadProcesoData
{
    public function __construct(
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
    ) {
    }

    /**
     * @return array{id_activ: int, nom_activ: string}
     */
    public function execute(int $id_activ): array
    {
        $oActividad = $this->actividadAllRepository->findById($id_activ);

        return [
            'id_activ' => $id_activ,
            'nom_activ' => $oActividad === null ? '' : $oActividad->getNom_activ(),
        ];
    }
}
