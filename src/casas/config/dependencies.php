<?php

use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\infrastructure\repositories\PgIngresoRepository;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\infrastructure\repositories\PgUbiGastoRepository;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\infrastructure\repositories\PgGrupoCasaRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    IngresoRepositoryInterface::class => autowire(PgIngresoRepository::class),
    UbiGastoRepositoryInterface::class => autowire(PgUbiGastoRepository::class),
    GrupoCasaRepositoryInterface::class => autowire(PgGrupoCasaRepository::class),
];
