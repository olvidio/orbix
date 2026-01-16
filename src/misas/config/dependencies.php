<?php

use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\misas\infrastructure\repositories\PgEncargoCtrRepository;
use src\misas\infrastructure\repositories\PgEncargoDiaRepository;
use src\misas\infrastructure\repositories\PgInicialesSacdRepository;
use src\misas\infrastructure\repositories\PgPlantillaRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    EncargoCtrRepositoryInterface::class => autowire(PgEncargoCtrRepository::class),
    EncargoDiaRepositoryInterface::class => autowire(PgEncargoDiaRepository::class),
    InicialesSacdRepositoryInterface::class => autowire(PgInicialesSacdRepository::class),
    PlantillaRepositoryInterface::class => autowire(PgPlantillaRepository::class),

    // Services
    InicialesSacdService::class => autowire(InicialesSacdService::class),
];
