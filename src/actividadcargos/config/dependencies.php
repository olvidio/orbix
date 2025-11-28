<?php

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\infrastructure\repositories\PgCargoRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    CargoRepositoryInterface::class => autowire(PgCargoRepository::class),
];
