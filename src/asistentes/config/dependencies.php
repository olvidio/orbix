<?php

use src\asistentes\application\ActivPendientesSelectData;
use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\AsistenteGuardar;
use src\asistentes\application\AsistenteMoverData;
use src\asistentes\application\AsistentePlazaAsignar;
use src\asistentes\application\FormActividadesDeUnaPersonaData;
use src\asistentes\application\FormAsistentesAUnaActividadData;
use src\asistentes\application\ListaActivCtrData;
use src\asistentes\application\ListaAsisConjuntoActivData;
use src\asistentes\application\ListaAsistentesData;
use src\asistentes\application\ListaEstCtrData;
use src\asistentes\application\ListaPlazasConjuntoActividades;
use src\asistentes\application\ListaUltimaActivData;
use src\asistentes\application\ListaUltimQueCtrData;
use src\asistentes\application\PlazaPropietarioAsignacion;
use src\asistentes\domain\InfoAsistenteDl;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\application\QueCtrListaData;
use src\asistentes\application\Select_actividades_de_una_persona;
use src\asistentes\application\Select_asistentes_a_una_actividad;
use src\asistentes\application\TablaPeticionesData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\infrastructure\persistence\postgresql\PgAsistenteDlRepository;
use src\asistentes\infrastructure\persistence\postgresql\PgAsistenteExRepository;
use src\asistentes\infrastructure\persistence\postgresql\PgAsistenteOutRepository;
use src\asistentes\infrastructure\persistence\postgresql\PgAsistentePubRepository;
use src\asistentes\infrastructure\persistence\postgresql\PgAsistenteRepository;
use function DI\autowire;

return [
// Mapeos de Interfaces a Implementaciones
    AsistenteRepositoryInterface::class => autowire(PgAsistenteRepository::class),
    AsistenteDlRepositoryInterface::class => autowire(PgAsistenteDlRepository::class),
    AsistenteExRepositoryInterface::class => autowire(PgAsistenteExRepository::class),
    AsistentePubRepositoryInterface::class => autowire(PgAsistentePubRepository::class),
    AsistenteOutRepositoryInterface::class => autowire(PgAsistenteOutRepository::class),

// Servicios de Aplicación
    AsistenteApplicationService::class => autowire(AsistenteApplicationService::class),
    AsistenteActividadService::class => autowire(AsistenteActividadService::class),

// Casos de uso / Application classes
    ActivPendientesSelectData::class => autowire(ActivPendientesSelectData::class),
    AsistenteEliminar::class => autowire(AsistenteEliminar::class),
    AsistenteGuardar::class => autowire(AsistenteGuardar::class),
    AsistenteMoverData::class => autowire(AsistenteMoverData::class),
    AsistentePlazaAsignar::class => autowire(AsistentePlazaAsignar::class),
    FormActividadesDeUnaPersonaData::class => autowire(FormActividadesDeUnaPersonaData::class),
    FormAsistentesAUnaActividadData::class => autowire(FormAsistentesAUnaActividadData::class),
    ListaActivCtrData::class => autowire(ListaActivCtrData::class),
    ListaAsisConjuntoActivData::class => autowire(ListaAsisConjuntoActivData::class),
    ListaAsistentesData::class => autowire(ListaAsistentesData::class),
    ListaEstCtrData::class => autowire(ListaEstCtrData::class),
    ListaPlazasConjuntoActividades::class => autowire(ListaPlazasConjuntoActividades::class),
    ListaUltimaActivData::class => autowire(ListaUltimaActivData::class),
    ListaUltimQueCtrData::class => autowire(ListaUltimQueCtrData::class),
    QueCtrListaData::class => autowire(QueCtrListaData::class),
    TablaPeticionesData::class => autowire(TablaPeticionesData::class),
    Select_actividades_de_una_persona::class => autowire(Select_actividades_de_una_persona::class),
    Select_asistentes_a_una_actividad::class => autowire(Select_asistentes_a_una_actividad::class),
    PlazaPropietarioAsignacionInterface::class => autowire(PlazaPropietarioAsignacion::class),
    PlazaPropietarioAsignacion::class => autowire(PlazaPropietarioAsignacion::class),
    InfoAsistenteDl::class => autowire(InfoAsistenteDl::class),
];
