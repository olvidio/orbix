<?php

use src\actividadplazas\application\GestionPlazasData;
use src\actividadplazas\application\GestionPlazasUpdate;
use src\actividadplazas\application\PeticionesActivData;
use src\actividadplazas\application\PeticionesEliminar;
use src\actividadplazas\application\PeticionesGuardar;
use src\actividadplazas\application\PeticionesIncorporar;
use src\actividadplazas\application\PlazasBalanceData;
use src\actividadplazas\application\PlazasBalanceQueData;
use src\actividadplazas\application\PlazasCeder;
use src\actividadplazas\application\PlazasDlEdicion;
use src\actividadplazas\application\PosiblesPropietariosData;
use src\actividadplazas\application\ResumenPlazasData;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\infrastructure\persistence\postgresql\PgActividadPlazasDlRepository;
use src\actividadplazas\infrastructure\persistence\postgresql\PgActividadPlazasRepository;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\infrastructure\persistence\postgresql\PgPlazaPeticionRepository;
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

    // Casos de uso / Application classes
    GestionPlazasData::class => autowire(GestionPlazasData::class),
    GestionPlazasUpdate::class => autowire(GestionPlazasUpdate::class),
    PeticionesActivData::class => autowire(PeticionesActivData::class),
    PeticionesEliminar::class => autowire(PeticionesEliminar::class),
    PeticionesGuardar::class => autowire(PeticionesGuardar::class),
    PeticionesIncorporar::class => autowire(PeticionesIncorporar::class),
    PlazasBalanceData::class => autowire(PlazasBalanceData::class),
    PlazasBalanceQueData::class => autowire(PlazasBalanceQueData::class),
    PlazasCeder::class => autowire(PlazasCeder::class),
    PlazasDlEdicion::class => autowire(PlazasDlEdicion::class),
    PosiblesPropietariosData::class => autowire(PosiblesPropietariosData::class),
    ResumenPlazasData::class => autowire(ResumenPlazasData::class),
];
