<?php

use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\infrastructure\repositories\PgActividadPlazasDlRepository;
use src\actividadplazas\infrastructure\repositories\PgActividadPlazasRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ActividadPlazasDlRepositoryInterface::class => autowire(PgActividadPlazasDlRepository::class),
    ActividadPlazasRepositoryInterface::class => autowire(PgActividadPlazasRepository::class),
];
