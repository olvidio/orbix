<?php

use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\infrastructure\repositories\PgActividadExRepository;
use src\actividades\infrastructure\repositories\PgActividadPubRepository;
use src\actividades\infrastructure\repositories\PgActividadRepository;
use src\actividades\infrastructure\repositories\PgNivelStgrRepository;
use src\actividades\infrastructure\repositories\PgRepeticionRepository;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\infrastructure\repositories\PgActividadAllRepository;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\infrastructure\repositories\PgActividadDlRepository;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\infrastructure\repositories\PgImportadaRepository;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\infrastructure\repositories\PgTipoDeActividadRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    NivelStgrRepositoryInterface::class => autowire(PgNivelStgrRepository::class),
    RepeticionRepositoryInterface::class => autowire(PgRepeticionRepository::class),
    ActividadAllRepositoryInterface::class => autowire(PgActividadAllRepository::class),
    ActividadDlRepositoryInterface::class => autowire(PgActividadDlRepository::class),
    ActividadExRepositoryInterface::class => autowire(PgActividadExRepository::class),
    ActividadPubRepositoryInterface::class => autowire(PgActividadPubRepository::class),
    ActividadRepositoryInterface::class => autowire(PgActividadRepository::class),
    ImportadaRepositoryInterface::class => autowire(PgImportadaRepository::class),
    TipoDeActividadRepositoryInterface::class => autowire(PgTipoDeActividadRepository::class),
];
