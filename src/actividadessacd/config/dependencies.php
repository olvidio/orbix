<?php

use src\actividadessacd\application\ComSacdActivPeriodoPageData;
use src\actividadessacd\application\ComunicacionActividadesSacdData;
use src\actividadessacd\application\ComunicacionActividadesSacdEnviar;
use src\actividadessacd\application\ListaActividadesSacdData;
use src\actividadessacd\application\LocalesDesplegableData;
use src\actividadessacd\application\SacdAsignar;
use src\actividadessacd\application\SacdAsignarAuto;
use src\actividadessacd\application\SacdEliminar;
use src\actividadessacd\application\SacdReordenar;
use src\actividadessacd\application\SacdsDisponiblesData;
use src\actividadessacd\application\SacdsEncargadosData;
use src\actividadessacd\application\SolapesSacdData;
use src\actividadessacd\application\TextoComunicacionData;
use src\actividadessacd\application\TextoComunicacionGuardar;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\infrastructure\persistence\postgresql\PgActividadSacdTextoRepository;
use function DI\autowire;

return [
    ActividadSacdTextoRepositoryInterface::class => autowire(PgActividadSacdTextoRepository::class),

    ActividadesSacdHelper::class => autowire(ActividadesSacdHelper::class),
    ComunicarActividadesSacdService::class => autowire(ComunicarActividadesSacdService::class),

    ComSacdActivPeriodoPageData::class => autowire(ComSacdActivPeriodoPageData::class),
    ComunicacionActividadesSacdData::class => autowire(ComunicacionActividadesSacdData::class),
    ComunicacionActividadesSacdEnviar::class => autowire(ComunicacionActividadesSacdEnviar::class),
    ListaActividadesSacdData::class => autowire(ListaActividadesSacdData::class),
    LocalesDesplegableData::class => autowire(LocalesDesplegableData::class),
    SacdAsignar::class => autowire(SacdAsignar::class),
    SacdAsignarAuto::class => autowire(SacdAsignarAuto::class),
    SacdEliminar::class => autowire(SacdEliminar::class),
    SacdReordenar::class => autowire(SacdReordenar::class),
    SacdsDisponiblesData::class => autowire(SacdsDisponiblesData::class),
    SacdsEncargadosData::class => autowire(SacdsEncargadosData::class),
    SolapesSacdData::class => autowire(SolapesSacdData::class),
    TextoComunicacionData::class => autowire(TextoComunicacionData::class),
    TextoComunicacionGuardar::class => autowire(TextoComunicacionGuardar::class),
];
