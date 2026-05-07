<?php

use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\infrastructure\persistence\postgresql\PgPersonaBDURepository;
use src\dbextern\infrastructure\persistence\postgresql\PgIdMatchPersonaRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    PersonaBDURepositoryInterface::class => autowire(PgPersonaBDURepository::class),
    IdMatchPersonaRepositoryInterface::class => autowire(PgIdMatchPersonaRepository::class),
];
