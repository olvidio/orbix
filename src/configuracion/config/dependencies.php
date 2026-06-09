<?php

use src\configuracion\application\ModulosFormData;
use src\configuracion\application\ModulosSelectData;
use src\configuracion\application\ModulosUpdateAction;
use src\configuracion\application\ObtenerConfigSnapshot;
use src\configuracion\application\PeriodoCalendarioEscolarData;
use src\configuracion\domain\InfoApps as InfoAppsDomain;
use src\configuracion\domain\InfoModsInstalled;
use src\configuracion\domain\ModulosConfig;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\infrastructure\persistence\postgresql\PgAppRepository;
use src\configuracion\infrastructure\persistence\postgresql\PgConfigSchemaRepository;
use src\configuracion\infrastructure\persistence\postgresql\PgModuloInstaladoRepository;
use src\configuracion\infrastructure\persistence\postgresql\PgModuloRepository;
use function DI\autowire;

return [
    AppRepositoryInterface::class => autowire(PgAppRepository::class),
    ConfigSchemaRepositoryInterface::class => autowire(PgConfigSchemaRepository::class),
    ModuloInstaladoRepositoryInterface::class => autowire(PgModuloInstaladoRepository::class),
    ModuloRepositoryInterface::class => autowire(PgModuloRepository::class),

    InfoAppsDomain::class => autowire(InfoAppsDomain::class),
    InfoModsInstalled::class => autowire(InfoModsInstalled::class),
    ModulosConfig::class => autowire(ModulosConfig::class),

    ModulosFormData::class => autowire(ModulosFormData::class),
    ModulosSelectData::class => autowire(ModulosSelectData::class),
    ModulosUpdateAction::class => autowire(ModulosUpdateAction::class),
    ObtenerConfigSnapshot::class => autowire(ObtenerConfigSnapshot::class),
    PeriodoCalendarioEscolarData::class => autowire(PeriodoCalendarioEscolarData::class),
];
