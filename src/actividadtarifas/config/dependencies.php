<?php

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\infrastructure\repositories\PgTipoTarifaRepository;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\infrastructure\repositories\PgRelacionTarifaTipoActividadRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    TipoTarifaRepositoryInterface::class => autowire(PgTipoTarifaRepository::class),
    RelacionTarifaTipoActividadRepositoryInterface::class => autowire(PgRelacionTarifaTipoActividadRepository::class),
];
