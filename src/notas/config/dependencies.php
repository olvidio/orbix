<?php

use function DI\autowire;

use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\contracts\ActaExRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalExRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaCertificadoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;

use src\notas\application\ActaEliminar;
use src\notas\application\ActaImprimirPresentacionData;
use src\notas\application\ActaModificar;
use src\notas\application\ActaNueva;
use src\notas\application\ActaPdfEliminar;
use src\notas\application\ActaPdfSubir;
use src\notas\application\ActaSelectData;
use src\notas\application\ActaVerFormData;
use src\notas\application\ActividadesBuscarData;
use src\notas\application\AsigFaltanPersonasSelectTablaData;
use src\notas\application\AsigFaltanSelectTablaData;
use src\notas\application\AsignaturasPendientes;
use src\notas\application\AsignaturasPendientesData;
use src\notas\application\AsignaturasPendientesResumenData;
use src\notas\application\AsignaturasSearchData;
use src\notas\application\BuscarActaData;
use src\notas\application\CentroEstudiosLookup;
use src\notas\application\ComprobarNotasConstantsData;
use src\notas\application\DatosActa;
use src\notas\application\EditarPersonaNota;
use src\notas\application\ExaminadoresSearchData;
use src\notas\application\InformeStgrAgregados;
use src\notas\application\InformeStgrNumerarios;
use src\notas\application\InformeStgrProfesores;
use src\notas\application\ListadoAnualActasData;
use src\notas\application\NotaPersonaFormData;
use src\notas\application\NotasDeUnaPersonaData;
use src\notas\application\PersonaNotaEditar;
use src\notas\application\PersonaNotaEliminar;
use src\notas\application\PersonaNotaNueva;
use src\notas\application\PosiblesOpcionalesData;
use src\notas\application\PosiblesPreceptoresData;
use src\notas\application\Select_notas_de_una_persona;
use src\notas\application\TablaAlumnosAsignaturas;
use src\notas\application\Tesera;
use src\notas\application\TesseraCopiar;
use src\notas\application\TesseraCopiarSelectData;
use src\notas\application\TesseraImprimirData;
use src\notas\application\TesseraVerData;
use src\notas\application\support\ResumenFactory;
use src\notas\application\services\ResumenTempTablesService;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaTribunalSync;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\infrastructure\persistence\postgresql\PgActaDlRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaExRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalDlRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalExRepository;
use src\notas\infrastructure\persistence\postgresql\PgActaTribunalRepository;
use src\notas\infrastructure\persistence\postgresql\PgNotaRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaCertificadoRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaDlRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaOtraRegionStgrRepository;
use src\notas\infrastructure\persistence\postgresql\PgPersonaNotaRepository;

return [

    NotaRepositoryInterface::class => autowire(PgNotaRepository::class),
    ActaRepositoryInterface::class => autowire(PgActaRepository::class),
    ActaDlRepositoryInterface::class => autowire(PgActaDlRepository::class),
    ActaExRepositoryInterface::class => autowire(PgActaExRepository::class),
    ActaTribunalRepositoryInterface::class => autowire(PgActaTribunalRepository::class),
    ActaTribunalDlRepositoryInterface::class => autowire(PgActaTribunalDlRepository::class),
    ActaTribunalExRepositoryInterface::class => autowire(PgActaTribunalExRepository::class),
    PersonaNotaDlRepositoryInterface::class => autowire(PgPersonaNotaDlRepository::class),
    PersonaNotaCertificadoRepositoryInterface::class => autowire(PgPersonaNotaCertificadoRepository::class),
    PersonaNotaRepositoryInterface::class => autowire(PgPersonaNotaRepository::class),
    PersonaNotaOtraRegionStgrRepositoryInterface::class => autowire(PgPersonaNotaOtraRegionStgrRepository::class),
    ActaEliminar::class => autowire(ActaEliminar::class),
    ActaImprimirPresentacionData::class => autowire(ActaImprimirPresentacionData::class),
    ActaModificar::class => autowire(ActaModificar::class),
    ActaNueva::class => autowire(ActaNueva::class),
    ActaPdfEliminar::class => autowire(ActaPdfEliminar::class),
    ActaPdfSubir::class => autowire(ActaPdfSubir::class),
    ActaSelectData::class => autowire(ActaSelectData::class),
    ActaVerFormData::class => autowire(ActaVerFormData::class),
    ActividadesBuscarData::class => autowire(ActividadesBuscarData::class),
    AsigFaltanPersonasSelectTablaData::class => autowire(AsigFaltanPersonasSelectTablaData::class),
    AsigFaltanSelectTablaData::class => autowire(AsigFaltanSelectTablaData::class),
    AsignaturasPendientes::class => autowire(AsignaturasPendientes::class),
    AsignaturasPendientesData::class => autowire(AsignaturasPendientesData::class),
    AsignaturasPendientesResumenData::class => autowire(AsignaturasPendientesResumenData::class),
    AsignaturasSearchData::class => autowire(AsignaturasSearchData::class),
    BuscarActaData::class => autowire(BuscarActaData::class),
    CentroEstudiosLookup::class => autowire(CentroEstudiosLookup::class),
    ComprobarNotasConstantsData::class => autowire(ComprobarNotasConstantsData::class),
    DatosActa::class => autowire(DatosActa::class),
    ExaminadoresSearchData::class => autowire(ExaminadoresSearchData::class),
    InformeStgrAgregados::class => autowire(InformeStgrAgregados::class),
    InformeStgrNumerarios::class => autowire(InformeStgrNumerarios::class),
    InformeStgrProfesores::class => autowire(InformeStgrProfesores::class),
    ListadoAnualActasData::class => autowire(ListadoAnualActasData::class),
    NotaPersonaFormData::class => autowire(NotaPersonaFormData::class),
    NotasDeUnaPersonaData::class => autowire(NotasDeUnaPersonaData::class),
    PersonaNotaEditar::class => autowire(PersonaNotaEditar::class),
    PersonaNotaEliminar::class => autowire(PersonaNotaEliminar::class),
    PersonaNotaNueva::class => autowire(PersonaNotaNueva::class),
    PosiblesOpcionalesData::class => autowire(PosiblesOpcionalesData::class),
    PosiblesPreceptoresData::class => autowire(PosiblesPreceptoresData::class),
    Select_notas_de_una_persona::class => autowire(Select_notas_de_una_persona::class),
    TablaAlumnosAsignaturas::class => autowire(TablaAlumnosAsignaturas::class),
    Tesera::class => autowire(Tesera::class),
    TesseraCopiar::class => autowire(TesseraCopiar::class),
    TesseraCopiarSelectData::class => autowire(TesseraCopiarSelectData::class),
    TesseraImprimirData::class => autowire(TesseraImprimirData::class),
    TesseraVerData::class => autowire(TesseraVerData::class),
    ResumenTempTablesService::class => autowire(ResumenTempTablesService::class),
    ResumenFactory::class => autowire(ResumenFactory::class),
    ActaDlGuard::class => autowire(ActaDlGuard::class),
    ActaTribunalSync::class => autowire(ActaTribunalSync::class),
    PersonaNotaInputParser::class => autowire(PersonaNotaInputParser::class),
];
