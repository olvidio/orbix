<?php

use src\ubiscamas\domain\contracts\HabitacionRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\contracts\CamaRepositoryInterface;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\infrastructure\repositories\PgHabitacionRepository;
use src\ubiscamas\infrastructure\repositories\PgHabitacionDlRepository;
use src\ubiscamas\infrastructure\repositories\PgCamaRepository;
use src\ubiscamas\infrastructure\repositories\PgCamaDlRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    // 'autowire()' le dice a PHP-DI: "Intenta inyectar el PDO automáticamente en el constructor de Pg...Repository"
    HabitacionRepositoryInterface::class => autowire(PgHabitacionRepository::class),
    HabitacionDlRepositoryInterface::class => autowire(PgHabitacionDlRepository::class),
    CamaRepositoryInterface::class => autowire(PgCamaRepository::class),
    CamaDlRepositoryInterface::class => autowire(PgCamaDlRepository::class),
];
