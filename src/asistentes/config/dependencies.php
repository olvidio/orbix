<?php

use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\infrastructure\repositories\PgAsistenteDlRepository;
use src\asistentes\infrastructure\repositories\PgAsistenteExRepository;
use src\asistentes\infrastructure\repositories\PgAsistenteOutRepository;
use src\asistentes\infrastructure\repositories\PgAsistentePubRepository;
use src\asistentes\infrastructure\repositories\PgAsistenteRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    AsistenteRepositoryInterface::class => autowire(PgAsistenteRepository::class),
    AsistenteDlRepositoryInterface::class => autowire(PgAsistenteDlRepository::class),
    AsistenteExRepositoryInterface::class => autowire(PgAsistenteExRepository::class),
    AsistentePubRepositoryInterface::class => autowire(PgAsistentePubRepository::class),
    AsistenteOutRepositoryInterface::class => autowire(PgAsistenteOutRepository::class),

// Servicios de Aplicación
    AsistenteApplicationService::class => autowire(AsistenteApplicationService::class),
    AsistenteActividadService::class => autowire(AsistenteActividadService::class),
];
