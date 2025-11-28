<?php

use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorTipoRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    ProfesorTipoRepositoryInterface::class => autowire(PgProfesorTipoRepository::class),
];
