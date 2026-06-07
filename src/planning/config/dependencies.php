<?php

use src\planning\application\ActividadesDePersonaService;
use src\planning\application\ActividadesPorCasasService;
use src\planning\application\ActividadesPorZonasService;
use src\planning\application\CasaPeriodosForPlanning;
use src\planning\application\PlanningCasaQueFormData;
use src\planning\application\PlanningCasaVerData;
use src\planning\application\PlanningCtrSelectData;
use src\planning\application\PlanningPersonaRepositoryPicker;
use src\planning\application\PlanningPersonaSelectData;
use src\planning\application\PlanningPersonaVerData;
use src\planning\application\PlanningZonesQueData;
use src\planning\application\PlanningZonesSelectData;
use function DI\autowire;

return [
    // Application services
    ActividadesDePersonaService::class => autowire(ActividadesDePersonaService::class),
    ActividadesPorCasasService::class => autowire(ActividadesPorCasasService::class),
    ActividadesPorZonasService::class => autowire(ActividadesPorZonasService::class),
    CasaPeriodosForPlanning::class => autowire(CasaPeriodosForPlanning::class),
    PlanningPersonaRepositoryPicker::class => autowire(PlanningPersonaRepositoryPicker::class),

    // Casos de uso / Application classes
    PlanningCasaQueFormData::class => autowire(PlanningCasaQueFormData::class),
    PlanningCasaVerData::class => autowire(PlanningCasaVerData::class),
    PlanningCtrSelectData::class => autowire(PlanningCtrSelectData::class),
    PlanningPersonaSelectData::class => autowire(PlanningPersonaSelectData::class),
    PlanningPersonaVerData::class => autowire(PlanningPersonaVerData::class),
    PlanningZonesQueData::class => autowire(PlanningZonesQueData::class),
    PlanningZonesSelectData::class => autowire(PlanningZonesSelectData::class),
];
