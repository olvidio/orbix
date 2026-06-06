<?php

use src\actividades\application\ActividadCambiarTipo;
use src\actividades\application\ActividadDuplicar;
use src\actividades\application\ActividadEditar;
use src\actividades\application\ActividadEliminar;
use src\actividades\application\ActividadFaseCompletadaDatos;
use src\actividades\application\ActividadFasesCompletadasDatos;
use src\actividades\application\ActividadImportar;
use src\actividades\application\ActividadLugar;
use src\actividades\application\ActividadNueva;
use src\actividades\application\ActividadNuevoCurso;
use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\actividades\application\ActividadPublicar;
use src\actividades\application\ActividadQueDatos;
use src\actividades\application\ActividadQueFiltrosBloque;
use src\actividades\application\ActividadSelectListado;
use src\actividades\application\ActividadSelectUbiData;
use src\actividades\application\ActividadStatusLabelsDatos;
use src\actividades\application\ActividadTipoGetActividad;
use src\actividades\application\ActividadTipoGetAsistentes;
use src\actividades\application\ActividadTipoGetDlOrg;
use src\actividades\application\ActividadTipoGetFiltroLugar;
use src\actividades\application\ActividadTipoGetIdTarifa;
use src\actividades\application\ActividadTipoGetLugar;
use src\actividades\application\ActividadTipoGetNomTipo;
use src\actividades\application\ActividadTipoGetNomTipoTabla;
use src\actividades\application\ActividadTipoGetNivelStgrDefecto;
use src\actividades\application\ActividadVerDatos;
use src\actividades\application\BorrarActividad;
use src\actividades\application\CalendarioListasDatos;
use src\actividades\application\ListaActivTabla;
use src\actividades\application\ListaActividadesSgListado;
use src\actividades\application\ListaCentrosActivDatos;
use src\actividades\application\ListaSrCsvListado;
use src\actividades\application\ListaSrCsvQueDatos;
use src\actividades\application\TipoActivEliminar;
use src\actividades\application\TipoActivFormModificar;
use src\actividades\application\TipoActivFormNuevo;
use src\actividades\application\TipoActivLista;
use src\actividades\application\TipoActivMetadata;
use src\actividades\application\TipoActivNuevo;
use src\actividades\application\TipoActivUpdate;
use src\actividades\domain\InfoTipoRepeticion;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\infrastructure\persistence\postgresql\PgActividadAllRepository;
use src\actividades\infrastructure\persistence\postgresql\PgActividadDlRepository;
use src\actividades\infrastructure\persistence\postgresql\PgActividadExRepository;
use src\actividades\infrastructure\persistence\postgresql\PgActividadPubRepository;
use src\actividades\infrastructure\persistence\postgresql\PgActividadRepository;
use src\actividades\infrastructure\persistence\postgresql\PgImportadaRepository;
use src\actividades\infrastructure\persistence\postgresql\PgRepeticionRepository;
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

    // Casos de uso / Application classes
    ActividadCambiarTipo::class => autowire(ActividadCambiarTipo::class),
    ActividadDuplicar::class => autowire(ActividadDuplicar::class),
    ActividadEditar::class => autowire(ActividadEditar::class),
    ActividadEliminar::class => autowire(ActividadEliminar::class),
    ActividadFaseCompletadaDatos::class => autowire(ActividadFaseCompletadaDatos::class),
    ActividadFasesCompletadasDatos::class => autowire(ActividadFasesCompletadasDatos::class),
    ActividadImportar::class => autowire(ActividadImportar::class),
    ActividadLugar::class => autowire(ActividadLugar::class),
    ActividadNueva::class => autowire(ActividadNueva::class),
    ActividadNuevoCurso::class => autowire(ActividadNuevoCurso::class),
    ActividadNuevoCursoEjecutar::class => autowire(ActividadNuevoCursoEjecutar::class),
    ActividadPublicar::class => autowire(ActividadPublicar::class),
    ActividadQueDatos::class => autowire(ActividadQueDatos::class),
    ActividadQueFiltrosBloque::class => autowire(ActividadQueFiltrosBloque::class),
    ActividadSelectListado::class => autowire(ActividadSelectListado::class),
    ActividadSelectUbiData::class => autowire(ActividadSelectUbiData::class),
    ActividadStatusLabelsDatos::class => autowire(ActividadStatusLabelsDatos::class),
    ActividadTipoGetActividad::class => autowire(ActividadTipoGetActividad::class),
    ActividadTipoGetAsistentes::class => autowire(ActividadTipoGetAsistentes::class),
    ActividadTipoGetDlOrg::class => autowire(ActividadTipoGetDlOrg::class),
    ActividadTipoGetFiltroLugar::class => autowire(ActividadTipoGetFiltroLugar::class),
    ActividadTipoGetIdTarifa::class => autowire(ActividadTipoGetIdTarifa::class),
    ActividadTipoGetLugar::class => autowire(ActividadTipoGetLugar::class),
    ActividadTipoGetNomTipo::class => autowire(ActividadTipoGetNomTipo::class),
    ActividadTipoGetNomTipoTabla::class => autowire(ActividadTipoGetNomTipoTabla::class),
    ActividadTipoGetNivelStgrDefecto::class => autowire(ActividadTipoGetNivelStgrDefecto::class),
    ActividadVerDatos::class => autowire(ActividadVerDatos::class),
    BorrarActividad::class => autowire(BorrarActividad::class),
    CalendarioListasDatos::class => autowire(CalendarioListasDatos::class),
    ListaActivTabla::class => autowire(ListaActivTabla::class),
    ListaActividadesSgListado::class => autowire(ListaActividadesSgListado::class),
    ListaCentrosActivDatos::class => autowire(ListaCentrosActivDatos::class),
    ListaSrCsvListado::class => autowire(ListaSrCsvListado::class),
    ListaSrCsvQueDatos::class => autowire(ListaSrCsvQueDatos::class),
    TipoActivEliminar::class => autowire(TipoActivEliminar::class),
    TipoActivFormModificar::class => autowire(TipoActivFormModificar::class),
    TipoActivFormNuevo::class => autowire(TipoActivFormNuevo::class),
    TipoActivLista::class => autowire(TipoActivLista::class),
    TipoActivMetadata::class => autowire(TipoActivMetadata::class),
    TipoActivNuevo::class => autowire(TipoActivNuevo::class),
    TipoActivUpdate::class => autowire(TipoActivUpdate::class),
    InfoTipoRepeticion::class => autowire(InfoTipoRepeticion::class),
];
