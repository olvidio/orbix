<?php

use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\infrastructure\repositories\PgSituacionRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    SituacionRepositoryInterface::class => autowire(PgSituacionRepository::class),
];
