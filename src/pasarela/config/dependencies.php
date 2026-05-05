<?php

use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\infrastructure\persistence\postgresql\PgPasarelaConfigRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    PasarelaConfigRepositoryInterface::class => autowire(PgPasarelaConfigRepository::class),
];
