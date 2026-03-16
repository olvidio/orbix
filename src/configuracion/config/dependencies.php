<?php

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
    // Mapeos de Interfaces a Implementaciones
    AppRepositoryInterface::class => autowire(PgAppRepository::class),
    ConfigSchemaRepositoryInterface::class => autowire(PgConfigSchemaRepository::class),
    ModuloInstaladoRepositoryInterface::class => autowire(PgModuloInstaladoRepository::class),
    ModuloRepositoryInterface::class => autowire(PgModuloRepository::class),
];
