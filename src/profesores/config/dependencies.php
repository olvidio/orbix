<?php

use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorAmpliacionRepository;
use src\profesores\infrastructure\repositories\PgProfesorDirectorRepository;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorJuramentoRepository;
use src\profesores\infrastructure\repositories\PgProfesorLatinRepository;
use src\profesores\infrastructure\repositories\PgProfesorTipoRepository;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorStgrRepository;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorPublicacionRepository;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorCongresoRepository;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorDocenciaStgrRepository;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\infrastructure\repositories\PgProfesorTituloEstRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    ProfesorAmpliacionRepositoryInterface::class => autowire(PgProfesorAmpliacionRepository::class),
    ProfesorDirectorRepositoryInterface::class => autowire(PgProfesorDirectorRepository::class),
    ProfesorJuramentoRepositoryInterface::class => autowire(PgProfesorJuramentoRepository::class),
    ProfesorLatinRepositoryInterface::class => autowire(PgProfesorLatinRepository::class),
    ProfesorTipoRepositoryInterface::class => autowire(PgProfesorTipoRepository::class),
    ProfesorStgrRepositoryInterface::class => autowire(PgProfesorStgrRepository::class),
    ProfesorPublicacionRepositoryInterface::class => autowire(PgProfesorPublicacionRepository::class),
    ProfesorCongresoRepositoryInterface::class => autowire(PgProfesorCongresoRepository::class),
    ProfesorDocenciaStgrRepositoryInterface::class => autowire(PgProfesorDocenciaStgrRepository::class),
    ProfesorTituloEstRepositoryInterface::class => autowire(PgProfesorTituloEstRepository::class),
];
