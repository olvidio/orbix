<?php

use src\profesores\application\CongresosLista;
use src\profesores\application\DocenciaLista;
use src\profesores\application\FichaProfesorStgr;
use src\profesores\application\ListaPorDepartamentos;
use src\profesores\application\ProfesorAsignaturaQueData;
use src\profesores\application\ProfesoresAsignaturaLista;
use src\profesores\domain\InfoProfesorAmpliacion;
use src\profesores\domain\InfoProfesorCongreso;
use src\profesores\domain\InfoProfesorDirector;
use src\profesores\domain\InfoProfesorDocenciaStgr;
use src\profesores\domain\InfoProfesorJuramento;
use src\profesores\domain\InfoProfesorLatin;
use src\profesores\domain\InfoProfesorPublicacion;
use src\profesores\domain\InfoProfesorStgr;
use src\profesores\domain\InfoProfesorTipo;
use src\profesores\domain\InfoProfesorTituloEst;
use src\profesores\domain\ProfesorActividad;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\profesores\domain\services\ProfesorAsignaturaService;
use src\profesores\domain\services\ProfesorStgrService;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorAmpliacionRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorDirectorRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorJuramentoRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorLatinRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorTipoRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorStgrRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorPublicacionRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorCongresoRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorDocenciaStgrRepository;
use src\profesores\infrastructure\persistence\postgresql\PgProfesorTituloEstRepository;
use function DI\autowire;

return [
    // Repositorios
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

    // Domain services
    ProfesorStgrService::class => autowire(ProfesorStgrService::class),
    ProfesorAsignaturaService::class => autowire(ProfesorAsignaturaService::class),
    ProfesorActividad::class => autowire(ProfesorActividad::class),

    // Info* (DatosInfoRepo)
    InfoProfesorAmpliacion::class => autowire(InfoProfesorAmpliacion::class),
    InfoProfesorCongreso::class => autowire(InfoProfesorCongreso::class),
    InfoProfesorDirector::class => autowire(InfoProfesorDirector::class),
    InfoProfesorDocenciaStgr::class => autowire(InfoProfesorDocenciaStgr::class),
    InfoProfesorJuramento::class => autowire(InfoProfesorJuramento::class),
    InfoProfesorLatin::class => autowire(InfoProfesorLatin::class),
    InfoProfesorPublicacion::class => autowire(InfoProfesorPublicacion::class),
    InfoProfesorStgr::class => autowire(InfoProfesorStgr::class),
    InfoProfesorTipo::class => autowire(InfoProfesorTipo::class),
    InfoProfesorTituloEst::class => autowire(InfoProfesorTituloEst::class),

    // Casos de uso / Application
    CongresosLista::class => autowire(CongresosLista::class),
    DocenciaLista::class => autowire(DocenciaLista::class),
    FichaProfesorStgr::class => autowire(FichaProfesorStgr::class),
    ListaPorDepartamentos::class => autowire(ListaPorDepartamentos::class),
    ProfesorAsignaturaQueData::class => autowire(ProfesorAsignaturaQueData::class),
    ProfesoresAsignaturaLista::class => autowire(ProfesoresAsignaturaLista::class),
];
