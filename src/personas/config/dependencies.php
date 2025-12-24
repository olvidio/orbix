<?php

use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
use src\personas\infrastructure\repositories\PgPersonaExRepository;
use src\personas\infrastructure\repositories\PgPersonaNaxRepository;
use src\personas\infrastructure\repositories\PgPersonaPubRepository;
use src\personas\infrastructure\repositories\PgPersonaSacdRepository;
use src\personas\infrastructure\repositories\PgSituacionRepository;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\infrastructure\repositories\PgPersonaDlRepository;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\infrastructure\repositories\PgPersonaAgdRepository;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\infrastructure\repositories\PgPersonaNRepository;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\infrastructure\repositories\PgPersonaSRepository;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\infrastructure\repositories\PgPersonaSSSCRepository;
use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\personas\infrastructure\repositories\PgTelecoPersonaDlRepository;
use src\personas\infrastructure\repositories\PgTelecoPersonaExRepository;
use src\personas\infrastructure\repositories\PgTelecoPersonaRepository;
use src\personas\domain\services\TelecoPersonaService;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    SituacionRepositoryInterface::class => autowire(PgSituacionRepository::class),
    PersonaDlRepositoryInterface::class => autowire(PgPersonaDlRepository::class),
    PersonaAgdRepositoryInterface::class => autowire(PgPersonaAgdRepository::class),
    PersonaNRepositoryInterface::class => autowire(PgPersonaNRepository::class),
    PersonaNaxRepositoryInterface::class => autowire(PgPersonaNaxRepository::class),
    PersonaPubRepositoryInterface::class => autowire(PgPersonaPubRepository::class),
    PersonaExRepositoryInterface::class => autowire(PgPersonaExRepository::class),
    PersonaSRepositoryInterface::class => autowire(PgPersonaSRepository::class),
    PersonaSacdRepositoryInterface::class => autowire(PgPersonaSacdRepository::class),
    PersonaSSSCRepositoryInterface::class => autowire(PgPersonaSSSCRepository::class),
    TelecoPersonaRepositoryInterface::class => autowire(PgTelecoPersonaRepository::class),
    TelecoPersonaDlRepositoryInterface::class => autowire(PgTelecoPersonaDlRepository::class),
    TelecoPersonaExRepositoryInterface::class => autowire(PgTelecoPersonaExRepository::class),

    // Services
    TelecoPersonaService::class => autowire(TelecoPersonaService::class),
];
