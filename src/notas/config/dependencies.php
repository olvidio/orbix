<?php

use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\infrastructure\repositories\PgNotaRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    NotaRepositoryInterface::class => autowire(PgNotaRepository::class),
];
