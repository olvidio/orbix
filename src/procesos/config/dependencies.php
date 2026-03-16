<?php

use src\procesos\application\ProcesoActividadService;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgPermUsuarioActividadRepository;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgActividadFaseRepository;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgActividadTareaRepository;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgProcesoTipoRepository;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgTareaProcesoRepository;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgActividadProcesoTareaRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    PermUsuarioActividadRepositoryInterface::class => autowire(PgPermUsuarioActividadRepository::class),
    ActividadFaseRepositoryInterface::class => autowire(PgActividadFaseRepository::class),
    ActividadTareaRepositoryInterface::class => autowire(PgActividadTareaRepository::class),
    ProcesoTipoRepositoryInterface::class => autowire(PgProcesoTipoRepository::class),
    TareaProcesoRepositoryInterface::class => autowire(PgTareaProcesoRepository::class),
    ActividadProcesoTareaRepositoryInterface::class => autowire(PgActividadProcesoTareaRepository::class),


    // Services
    ProcesoActividadService::class => autowire(ProcesoActividadService::class),
];
