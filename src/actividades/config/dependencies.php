<?php

use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\infrastructure\repositories\PgNivelStgrRepository;
use src\actividades\infrastructure\repositories\PgRepeticionRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    NivelStgrRepositoryInterface::class => autowire(PgNivelStgrRepository::class),
    RepeticionRepositoryInterface::class => autowire(PgRepeticionRepository::class),
];
