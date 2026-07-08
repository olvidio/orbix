<?php

use src\personas\application\HomePersonaData;
use src\personas\application\PersonaEliminar;
use src\personas\application\PersonaUpdate;
use src\personas\application\PersonasEditarData;
use src\personas\application\PersonasSelectData;
use src\personas\application\services\PersonaFinderService;
use src\personas\application\StgrCambioData;
use src\personas\application\StgrUpdate;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\application\TrasladoFormData;
use src\personas\application\TrasladoUpdate;
use src\personas\domain\InfoSituacion;
use src\personas\domain\InfoTelecoPersona;
use src\personas\domain\InfoTraslado;
use src\personas\domain\InfoUltimaAsistencia;
use src\personas\domain\services\TelecoPersonaService;
use src\personas\domain\Trasladar;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\personas\infrastructure\persistence\postgresql\PgPersonaAllRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaExRepository;
use src\personas\infrastructure\persistence\postgresql\PersonaDlRepositoryFactory;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNaxRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaPubRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSacdRepository;
use src\personas\infrastructure\persistence\postgresql\PgSituacionRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaDlRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaAgdRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSRepository;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSSSCRepository;
use src\personas\infrastructure\persistence\postgresql\PgTelecoPersonaDlRepository;
use src\personas\infrastructure\persistence\postgresql\PgTelecoPersonaExRepository;
use src\personas\infrastructure\persistence\postgresql\PgTelecoPersonaRepository;
use src\personas\infrastructure\persistence\postgresql\PgTrasladoRepository;
use src\personas\infrastructure\persistence\postgresql\PgUltimaAsistenciaRepository;
use function DI\autowire;
use function DI\get;

return [
    // Mapeo simple: Interfaz => Clase
    SituacionRepositoryInterface::class => autowire(PgSituacionRepository::class),
    PersonaAllRepositoryInterface::class => autowire(PgPersonaAllRepository::class),
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
    TrasladoRepositoryInterface::class => autowire(PgTrasladoRepository::class),
    UltimaAsistenciaRepositoryInterface::class => autowire(PgUltimaAsistenciaRepository::class),

    // Domain services
    TelecoPersonaService::class => autowire(TelecoPersonaService::class),
    Trasladar::class => autowire(Trasladar::class),

    // Info* (DatosInfoRepo)
    InfoSituacion::class => autowire(InfoSituacion::class),
    InfoTelecoPersona::class => autowire(InfoTelecoPersona::class),
    InfoTraslado::class => autowire(InfoTraslado::class),
    InfoUltimaAsistencia::class => autowire(InfoUltimaAsistencia::class),

    // Application services
    PersonaFinderService::class => autowire(PersonaFinderService::class)
        ->constructor(
            get(PersonaDlRepositoryFactoryInterface::class),
            get(PersonaPubRepositoryInterface::class),
            get(PersonaExRepositoryInterface::class),
        ),
    PersonaRepositoryResolver::class => autowire(PersonaRepositoryResolver::class),

    // Casos de uso / Application classes
    HomePersonaData::class => autowire(HomePersonaData::class),
    PersonaEliminar::class => autowire(PersonaEliminar::class),
    PersonaUpdate::class => autowire(PersonaUpdate::class),
    PersonasEditarData::class => autowire(PersonasEditarData::class),
    PersonasSelectData::class => autowire(PersonasSelectData::class),
    StgrCambioData::class => autowire(StgrCambioData::class),
    StgrUpdate::class => autowire(StgrUpdate::class),
    TrasladoFormData::class => autowire(TrasladoFormData::class),
    TrasladoUpdate::class => autowire(TrasladoUpdate::class),
];
