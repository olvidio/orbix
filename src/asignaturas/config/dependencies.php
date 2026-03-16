<?php

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\infrastructure\persistence\postgresql\PgAsignaturaRepository;
use src\asignaturas\infrastructure\persistence\postgresql\PgAsignaturaTipoRepository;
use src\asignaturas\infrastructure\persistence\postgresql\PgDepartamentoRepository;
use src\asignaturas\infrastructure\persistence\postgresql\PgSectorRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    AsignaturaRepositoryInterface::class => autowire(PgAsignaturaRepository::class),
    AsignaturaTipoRepositoryInterface::class => autowire(PgAsignaturaTipoRepository::class),
    DepartamentoRepositoryInterface::class => autowire(PgDepartamentoRepository::class),
    SectorRepositoryInterface::class => autowire(PgSectorRepository::class),
];
