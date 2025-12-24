<?php

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\infrastructure\repositories\PgAppRepository;
use src\configuracion\infrastructure\repositories\PgConfigSchemaRepository;
use src\configuracion\infrastructure\repositories\PgModuloInstaladoRepository;
use src\configuracion\infrastructure\repositories\PgModuloRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    AppRepositoryInterface::class => autowire(PgAppRepository::class),
    ConfigSchemaRepositoryInterface::class => autowire(PgConfigSchemaRepository::class),
    ModuloInstaladoRepositoryInterface::class => autowire(PgModuloInstaladoRepository::class),
    ModuloRepositoryInterface::class => autowire(PgModuloRepository::class),
];
