<?php

use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaExRepository;
use src\personas\infrastructure\persistence\postgresql\PersonaDlRepositoryFactory;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNaxRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaPubRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSacdRepository;
use src\personas\infrastructure\persistence\postgresql\PgSituacionRepository;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaDlRepository;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaAgdRepository;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNRepository;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSRepository;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSSSCRepository;
use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgTelecoPersonaDlRepository;
use src\personas\infrastructure\persistence\postgresql\PgTelecoPersonaExRepository;
use src\personas\infrastructure\persistence\postgresql\PgTelecoPersonaRepository;
use src\personas\domain\services\TelecoPersonaService;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgTrasladoRepository;
use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgUltimaAsistenciaRepository;
use src\personas\application\services\PersonaFinderService;
use function DI\autowire;
use function DI\get;

return [
    // Mapeo simple: Interfaz => Clase
    SituacionRepositoryInterface::class => autowire(PgSituacionRepository::class),
    PersonaDlRepositoryInterface::class => autowire(PgPersonaDlRepository::class),
    PersonaDlRepositoryFactoryInterface::class => autowire(PersonaDlRepositoryFactory::class),
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
    TrasladoRepositoryInterface::class => autowire(PgTrasladoRepository::class),
    UltimaAsistenciaRepositoryInterface::class => autowire(PgUltimaAsistenciaRepository::class),

    // Application Services
    PersonaFinderService::class => autowire(PersonaFinderService::class)
        ->constructor(
            get(PersonaDlRepositoryFactoryInterface::class),
            get(PersonaPubRepositoryInterface::class),
            get(PersonaExRepositoryInterface::class)
        ),
];
