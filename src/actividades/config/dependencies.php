<?php

use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\infrastructure\persistence\postgresql\PgActividadExRepository;
use src\actividades\infrastructure\persistence\postgresql\PgActividadPubRepository;
use src\actividades\infrastructure\persistence\postgresql\PgActividadRepository;
use src\actividades\infrastructure\persistence\postgresql\PgRepeticionRepository;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\infrastructure\persistence\postgresql\PgActividadAllRepository;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\infrastructure\persistence\postgresql\PgActividadDlRepository;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\infrastructure\persistence\postgresql\PgImportadaRepository;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\infrastructure\persistence\postgresql\PgTipoDeActividadRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    RepeticionRepositoryInterface::class => autowire(PgRepeticionRepository::class),
    ActividadAllRepositoryInterface::class => autowire(PgActividadAllRepository::class),
    ActividadDlRepositoryInterface::class => autowire(PgActividadDlRepository::class),
    ActividadExRepositoryInterface::class => autowire(PgActividadExRepository::class),
    ActividadPubRepositoryInterface::class => autowire(PgActividadPubRepository::class),
    ActividadRepositoryInterface::class => autowire(PgActividadRepository::class),
    ImportadaRepositoryInterface::class => autowire(PgImportadaRepository::class),
    TipoDeActividadRepositoryInterface::class => autowire(PgTipoDeActividadRepository::class),
];
