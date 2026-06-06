<?php

use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\actividadtarifas\application\RelacionTarifaFormData;
use src\actividadtarifas\application\RelacionTarifaListaData;
use src\actividadtarifas\application\RelacionTarifaUpdate;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\application\TarifaUbiCopiar;
use src\actividadtarifas\application\TarifaUbiEliminar;
use src\actividadtarifas\application\TarifaUbiFormData;
use src\actividadtarifas\application\TarifaUbiListaData;
use src\actividadtarifas\application\TarifaUbiUpdate;
use src\actividadtarifas\application\TarifaUbiUpdateInc;
use src\actividadtarifas\application\TipoTarifaEliminar;
use src\actividadtarifas\application\TipoTarifaFormData;
use src\actividadtarifas\application\TipoTarifaListaData;
use src\actividadtarifas\application\TipoTarifaUpdate;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\infrastructure\persistence\postgresql\PgRelacionTarifaTipoActividadRepository;
use src\actividadtarifas\infrastructure\persistence\postgresql\PgTipoTarifaRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    TipoTarifaRepositoryInterface::class => autowire(PgTipoTarifaRepository::class),
    RelacionTarifaTipoActividadRepositoryInterface::class => autowire(PgRelacionTarifaTipoActividadRepository::class),

    // Application Services
    TipoTarifaDropdown::class => autowire(TipoTarifaDropdown::class),

    // Casos de uso / Application classes
    RelacionTarifaEliminar::class => autowire(RelacionTarifaEliminar::class),
    RelacionTarifaFormData::class => autowire(RelacionTarifaFormData::class),
    RelacionTarifaListaData::class => autowire(RelacionTarifaListaData::class),
    RelacionTarifaUpdate::class => autowire(RelacionTarifaUpdate::class),
    TarifaUbiCopiar::class => autowire(TarifaUbiCopiar::class),
    TarifaUbiEliminar::class => autowire(TarifaUbiEliminar::class),
    TarifaUbiFormData::class => autowire(TarifaUbiFormData::class),
    TarifaUbiListaData::class => autowire(TarifaUbiListaData::class),
    TarifaUbiUpdate::class => autowire(TarifaUbiUpdate::class),
    TarifaUbiUpdateInc::class => autowire(TarifaUbiUpdateInc::class),
    TipoTarifaEliminar::class => autowire(TipoTarifaEliminar::class),
    TipoTarifaFormData::class => autowire(TipoTarifaFormData::class),
    TipoTarifaListaData::class => autowire(TipoTarifaListaData::class),
    TipoTarifaUpdate::class => autowire(TipoTarifaUpdate::class),
];
