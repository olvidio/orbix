<?php

use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\infrastructure\persistence\postgresql\PgCargoOAsistente;
use src\actividadcargos\infrastructure\persistence\postgresql\PgCargoRepository;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\infrastructure\persistence\postgresql\PgActividadCargoDlRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    CargoRepositoryInterface::class => autowire(PgCargoRepository::class),
    CargoOAsistenteInterface::class => autowire(PgCargoOAsistente::class),
    ActividadCargoRepositoryInterface::class => autowire(PgActividadCargoDlRepository::class),
];
