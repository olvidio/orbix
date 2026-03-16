<?php

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\infrastructure\persistence\postgresql\PgActividadAsignaturaRepository;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\infrastructure\persistence\postgresql\PgActividadAsignaturaDlRepository;
use src\actividadestudios\infrastructure\persistence\postgresql\PgMatriculaDlRepository;
use src\actividadestudios\infrastructure\persistence\postgresql\PgMatriculaRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ActividadAsignaturaDlRepositoryInterface::class => autowire(PgActividadAsignaturaDlRepository::class),
    ActividadAsignaturaRepositoryInterface::class => autowire(PgActividadAsignaturaRepository::class),
    MatriculaDlRepositoryInterface::class => autowire(PgMatriculaDlRepository::class),
    MatriculaRepositoryInterface::class => autowire(PgMatriculaRepository::class),
];
