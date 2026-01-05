<?php

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\infrastructure\repositories\PgActividadAsignaturaRepository;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\infrastructure\repositories\PgActividadAsignaturaDlRepository;
use src\actividadestudios\infrastructure\repositories\PgMatriculaDlRepository;
use src\actividadestudios\infrastructure\repositories\PgMatriculaRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ActividadAsignaturaDlRepositoryInterface::class => autowire(PgActividadAsignaturaDlRepository::class),
    ActividadAsignaturaRepositoryInterface::class => autowire(PgActividadAsignaturaRepository::class),
    MatriculaDlRepositoryInterface::class => autowire(PgMatriculaDlRepository::class),
    MatriculaRepositoryInterface::class => autowire(PgMatriculaRepository::class),
];
