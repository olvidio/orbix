<?php

namespace src\actividades\application;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;

/**
 * Lista de id_fase con completado=true para una actividad (mismo criterio que
 * {@see ActividadProcesoTareaRepositoryInterface::getFasesCompletadas}).
 * Expuesto vía HTTP para que `frontend/` alimente
 * {@see \src\permisos\domain\PermisosActividades::setFasesCompletadas} sin contenedor DI.
 */
final class ActividadFasesCompletadasDatos
{
    public function __construct(
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @return array{fases_completadas: list<int>}
     */
    public function ejecutar(int $idActiv): array
    {
        if ($idActiv <= 0) {
            return ['fases_completadas' => []];
        }

        $repo = $this->actividadProcesoTareaRepository;
        $fases = $repo->getFasesCompletadas($idActiv);

        return ['fases_completadas' => array_values(array_map(static fn ($id) => (int) $id, $fases))];
    }
}
