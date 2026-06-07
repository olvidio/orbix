<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\notas\application\legacy\Resumen;
use src\notas\application\services\ResumenTempTablesService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;

/**
 * Fabrica instancias de {@see Resumen} legacy con dependencias inyectadas.
 */
final class ResumenFactory
{
    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly SectorRepositoryInterface $sectorRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly ProfesorDirectorRepositoryInterface $profesorDirectorRepository,
        private readonly DepartamentoRepositoryInterface $departamentoRepository,
        private readonly ResumenTempTablesService $tempTablesService,
    ) {
    }

    public function create(string $nom): Resumen
    {
        return new Resumen(
            $nom,
            $this->asignaturaRepository,
            $this->sectorRepository,
            $this->actividadAllRepository,
            $this->personaDlRepository,
            $this->profesorDirectorRepository,
            $this->departamentoRepository,
            $this->tempTablesService,
        );
    }
}
