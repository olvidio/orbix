<?php

use src\procesos\application\ActividadProcesoData;
use src\procesos\application\ActividadProcesoGenerar;
use src\procesos\application\ActividadProcesoGet;
use src\procesos\application\ActividadProcesoUpdate;
use src\procesos\application\ActividadQueFasesCuadro;
use src\procesos\application\FasesActivCambioActividadTipoHtml;
use src\procesos\application\FasesActivCambioGet;
use src\procesos\application\FasesActivCambioLista;
use src\procesos\application\FasesActivCambioTipoActividadHtmlData;
use src\procesos\application\FasesActivCambioUpdate;
use src\procesos\application\ProcesoActividadService;
use src\procesos\application\ProcesosClonar;
use src\procesos\application\ProcesosDepende;
use src\procesos\application\ProcesosEliminar;
use src\procesos\application\ProcesosGet;
use src\procesos\application\ProcesosGetListado;
use src\procesos\application\ProcesosRegenerar;
use src\procesos\application\ProcesosSelectData;
use src\procesos\application\ProcesosUpdate;
use src\procesos\application\ProcesosVerData;
use src\procesos\application\TipoActivProcesoAsignar;
use src\procesos\application\TipoActivProcesoLista;
use src\procesos\application\TipoActivProcesoLstPosibles;
use src\procesos\application\UsuarioPermActivData;
use src\procesos\application\UsuarioPermActivFases;
use src\procesos\domain\InfoFases;
use src\procesos\domain\InfoProcesoTipo;
use src\procesos\domain\InfoTareas;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\infrastructure\persistence\postgresql\PgActividadFaseRepository;
use src\procesos\infrastructure\persistence\postgresql\PgActividadProcesoTareaRepository;
use src\procesos\infrastructure\persistence\postgresql\PgActividadTareaRepository;
use src\procesos\infrastructure\persistence\postgresql\PgPermUsuarioActividadRepository;
use src\procesos\infrastructure\persistence\postgresql\PgProcesoTipoRepository;
use src\procesos\infrastructure\persistence\postgresql\PgTareaProcesoRepository;
use function DI\autowire;

return [
    // Repositorios
    PermUsuarioActividadRepositoryInterface::class => autowire(PgPermUsuarioActividadRepository::class),
    ActividadFaseRepositoryInterface::class => autowire(PgActividadFaseRepository::class),
    ActividadTareaRepositoryInterface::class => autowire(PgActividadTareaRepository::class),
    ProcesoTipoRepositoryInterface::class => autowire(PgProcesoTipoRepository::class),
    TareaProcesoRepositoryInterface::class => autowire(PgTareaProcesoRepository::class),
    ActividadProcesoTareaRepositoryInterface::class => autowire(PgActividadProcesoTareaRepository::class),

    // Info* (DatosInfoRepo)
    InfoFases::class => autowire(InfoFases::class),
    InfoTareas::class => autowire(InfoTareas::class),
    InfoProcesoTipo::class => autowire(InfoProcesoTipo::class),

    // Servicios / helpers application
    ProcesoActividadService::class => autowire(ProcesoActividadService::class),
    FasesActivCambioActividadTipoHtml::class => autowire(FasesActivCambioActividadTipoHtml::class),

    // Casos de uso / Application
    ActividadProcesoData::class => autowire(ActividadProcesoData::class),
    ActividadProcesoGenerar::class => autowire(ActividadProcesoGenerar::class),
    ActividadProcesoGet::class => autowire(ActividadProcesoGet::class),
    ActividadProcesoUpdate::class => autowire(ActividadProcesoUpdate::class),
    ActividadQueFasesCuadro::class => autowire(ActividadQueFasesCuadro::class),
    FasesActivCambioGet::class => autowire(FasesActivCambioGet::class),
    FasesActivCambioLista::class => autowire(FasesActivCambioLista::class),
    FasesActivCambioTipoActividadHtmlData::class => autowire(FasesActivCambioTipoActividadHtmlData::class),
    FasesActivCambioUpdate::class => autowire(FasesActivCambioUpdate::class),
    ProcesosClonar::class => autowire(ProcesosClonar::class),
    ProcesosDepende::class => autowire(ProcesosDepende::class),
    ProcesosEliminar::class => autowire(ProcesosEliminar::class),
    ProcesosGet::class => autowire(ProcesosGet::class),
    ProcesosGetListado::class => autowire(ProcesosGetListado::class),
    ProcesosRegenerar::class => autowire(ProcesosRegenerar::class),
    ProcesosSelectData::class => autowire(ProcesosSelectData::class),
    ProcesosUpdate::class => autowire(ProcesosUpdate::class),
    ProcesosVerData::class => autowire(ProcesosVerData::class),
    TipoActivProcesoAsignar::class => autowire(TipoActivProcesoAsignar::class),
    TipoActivProcesoLista::class => autowire(TipoActivProcesoLista::class),
    TipoActivProcesoLstPosibles::class => autowire(TipoActivProcesoLstPosibles::class),
    UsuarioPermActivData::class => autowire(UsuarioPermActivData::class),
    UsuarioPermActivFases::class => autowire(UsuarioPermActivFases::class),
];
