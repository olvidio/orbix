<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\security\HashB;

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
     * @return array{id_activ: int, nom_activ: string, ctx_generar: string}
     */
    public function execute(int $id_activ): array
    {
        $oActividad = $this->actividadAllRepository->findById($id_activ);

        return [
            'id_activ' => $id_activ,
            'nom_activ' => $oActividad === null ? '' : $oActividad->getNom_activ(),
            // Volver a generar el proceso (las fases) de esta actividad: `actividad_proceso_generar`.
            'ctx_generar' => HashB::sign('actividad_proceso_generar', ['id_activ' => $id_activ]),
        ];
    }
}
