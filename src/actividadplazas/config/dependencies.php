<?php

use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\infrastructure\repositories\PgActividadPlazasDlRepository;
use src\actividadplazas\infrastructure\repositories\PgActividadPlazasRepository;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\infrastructure\repositories\PgPlazaPeticionRepository;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use function DI\autowire;
use function DI\get;

return [
// Mapeos de Interfaces a Implementaciones
    ActividadPlazasDlRepositoryInterface::class => autowire(PgActividadPlazasDlRepository::class),
    ActividadPlazasRepositoryInterface::class => autowire(PgActividadPlazasRepository::class),
    PlazaPeticionRepositoryInterface::class => autowire(PgPlazaPeticionRepository::class),

    // Application Services
    ResumenPlazasService::class => autowire(ResumenPlazasService::class)
        ->constructor(
            get(ActividadAllRepositoryInterface::class),
            get(ActividadPlazasRepositoryInterface::class),
            get(DelegacionRepositoryInterface::class),
            get(AsistenteActividadService::class)
        ),
];
