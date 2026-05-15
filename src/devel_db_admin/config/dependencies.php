<?php

declare(strict_types=1);

use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\devel_db_admin\infrastructure\persistence\postgresql\PgMigracionAplicadaRepository;
use function DI\autowire;

return [
    MigracionAplicadaRepositoryInterface::class => autowire(PgMigracionAplicadaRepository::class),
];
