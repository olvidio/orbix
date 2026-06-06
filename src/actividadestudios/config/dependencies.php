<?php

use src\actividadestudios\application\ActaNotasData;
use src\actividadestudios\application\ActaNotasDefinitivasGrabar;
use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use src\actividadestudios\application\ActividadAsignaturaEditar;
use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\actividadestudios\application\ActividadAsignaturaNueva;
use src\actividadestudios\application\AsistenteObserv;
use src\actividadestudios\application\AsistenteObservEst;
use src\actividadestudios\application\AsistentePlanEstOk;
use src\actividadestudios\application\CaPosiblesData;
use src\actividadestudios\application\CaPosiblesQueData;
use src\actividadestudios\application\DocenciaActualizar;
use src\actividadestudios\application\E43CertificadoData;
use src\actividadestudios\application\FormAsignaturasDeUnaActividadData;
use src\actividadestudios\application\FormMatriculasDeUnaPersonaData;
use src\actividadestudios\application\ListaClasesCaData;
use src\actividadestudios\application\MatriculaAutomatica;
use src\actividadestudios\application\MatriculaEditar;
use src\actividadestudios\application\MatriculaEliminar;
use src\actividadestudios\application\MatriculaNueva;
use src\actividadestudios\application\MatriculasListaData;
use src\actividadestudios\application\MatriculasListaOtrasRData;
use src\actividadestudios\application\MatriculasPendientesData;
use src\actividadestudios\application\PlanEstudiosCaData;
use src\actividadestudios\application\PosiblesAsignaturasCaData;
use src\actividadestudios\application\ProfesoresDesplegableData;
use src\actividadestudios\application\Select_asignaturas_de_una_actividad;
use src\actividadestudios\application\Select_matriculas_de_una_actividad;
use src\actividadestudios\application\Select_matriculas_de_una_persona;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\actividadestudios\infrastructure\persistence\postgresql\PgActividadAsignaturaDlRepository;
use src\actividadestudios\infrastructure\persistence\postgresql\PgActividadAsignaturaRepository;
use src\actividadestudios\infrastructure\persistence\postgresql\PgMatriculaDlRepository;
use src\actividadestudios\infrastructure\persistence\postgresql\PgMatriculaRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    ActividadAsignaturaDlRepositoryInterface::class => autowire(PgActividadAsignaturaDlRepository::class),
    ActividadAsignaturaRepositoryInterface::class => autowire(PgActividadAsignaturaRepository::class),
    MatriculaDlRepositoryInterface::class => autowire(PgMatriculaDlRepository::class),
    MatriculaRepositoryInterface::class => autowire(PgMatriculaRepository::class),

    // Domain
    PosiblesCa::class => autowire(PosiblesCa::class),

    // Casos de uso / Application classes
    ActaNotasData::class => autowire(ActaNotasData::class),
    ActaNotasDefinitivasGrabar::class => autowire(ActaNotasDefinitivasGrabar::class),
    ActaNotasMatriculaGuardar::class => autowire(ActaNotasMatriculaGuardar::class),
    ActividadAsignaturaEditar::class => autowire(ActividadAsignaturaEditar::class),
    ActividadAsignaturaEliminar::class => autowire(ActividadAsignaturaEliminar::class),
    ActividadAsignaturaNueva::class => autowire(ActividadAsignaturaNueva::class),
    AsistenteObserv::class => autowire(AsistenteObserv::class),
    AsistenteObservEst::class => autowire(AsistenteObservEst::class),
    AsistentePlanEstOk::class => autowire(AsistentePlanEstOk::class),
    CaPosiblesData::class => autowire(CaPosiblesData::class),
    CaPosiblesQueData::class => autowire(CaPosiblesQueData::class),
    DocenciaActualizar::class => autowire(DocenciaActualizar::class),
    E43CertificadoData::class => autowire(E43CertificadoData::class),
    FormAsignaturasDeUnaActividadData::class => autowire(FormAsignaturasDeUnaActividadData::class),
    FormMatriculasDeUnaPersonaData::class => autowire(FormMatriculasDeUnaPersonaData::class),
    ListaClasesCaData::class => autowire(ListaClasesCaData::class),
    MatriculaAutomatica::class => autowire(MatriculaAutomatica::class),
    MatriculaEditar::class => autowire(MatriculaEditar::class),
    MatriculaEliminar::class => autowire(MatriculaEliminar::class),
    MatriculaNueva::class => autowire(MatriculaNueva::class),
    MatriculasListaData::class => autowire(MatriculasListaData::class),
    MatriculasListaOtrasRData::class => autowire(MatriculasListaOtrasRData::class),
    MatriculasPendientesData::class => autowire(MatriculasPendientesData::class),
    PlanEstudiosCaData::class => autowire(PlanEstudiosCaData::class),
    PosiblesAsignaturasCaData::class => autowire(PosiblesAsignaturasCaData::class),
    ProfesoresDesplegableData::class => autowire(ProfesoresDesplegableData::class),
    Select_asignaturas_de_una_actividad::class => autowire(Select_asignaturas_de_una_actividad::class),
    Select_matriculas_de_una_actividad::class => autowire(Select_matriculas_de_una_actividad::class),
    Select_matriculas_de_una_persona::class => autowire(Select_matriculas_de_una_persona::class),
];
