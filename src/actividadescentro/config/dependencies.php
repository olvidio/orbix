<?php

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\infrastructure\repositories\PgCentroEncargadoRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    CentroEncargadoRepositoryInterface::class => autowire(PgCentroEncargadoRepository::class),
];
