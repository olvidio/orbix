<?php

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\infrastructure\repositories\PgActividadSacdTextoRepository;
use src\actividadessacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\actividadessacd\infrastructure\repositories\PgEncargoSacdHorarioRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ActividadSacdTextoRepositoryInterface::class => autowire(PgActividadSacdTextoRepository::class),
    EncargoSacdHorarioRepositoryInterface::class => autowire(PgEncargoSacdHorarioRepository::class),
];
