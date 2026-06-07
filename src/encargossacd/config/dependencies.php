<?php

use src\encargossacd\application\CentrosPorFiltroOpciones;
use src\encargossacd\application\CtrFichaData;
use src\encargossacd\application\CtrFichaUpdate;
use src\encargossacd\application\CtrGetFichaData;
use src\encargossacd\application\EncargoComprobacionesCtr;
use src\encargossacd\application\EncargoCtrSelectData;
use src\encargossacd\application\EncargoHorarioSelectData;
use src\encargossacd\application\EncargoHorarioUpdate;
use src\encargossacd\application\EncargoHorarioVerData;
use src\encargossacd\application\EncargoLstTipoEncData;
use src\encargossacd\application\EncargoSacdHorarioUpdate;
use src\encargossacd\application\EncargoSacdHorarioVerData;
use src\encargossacd\application\EncargoSelectData;
use src\encargossacd\application\EncargoVerData;
use src\encargossacd\application\EncargoVerEditar;
use src\encargossacd\application\EncargoVerEliminar;
use src\encargossacd\application\EncargoVerNuevo;
use src\encargossacd\application\EncargoZonasSelectData;
use src\encargossacd\application\ListasAData;
use src\encargossacd\application\ListasBData;
use src\encargossacd\application\ListasCData;
use src\encargossacd\application\ListasClData;
use src\encargossacd\application\ListasComCtrData;
use src\encargossacd\application\ListasComSacdData;
use src\encargossacd\application\ListasComTxtData;
use src\encargossacd\application\ListasComTxtGet;
use src\encargossacd\application\ListasComTxtUpdate;
use src\encargossacd\application\ListasDData;
use src\encargossacd\application\ListasExigenciaCtrData;
use src\encargossacd\application\SacdAusenciasGetData;
use src\encargossacd\application\SacdAusenciasJefeZonaData;
use src\encargossacd\application\SacdAusenciasUpdate;
use src\encargossacd\application\SacdFichaData;
use src\encargossacd\application\SacdFichaUpdate;
use src\encargossacd\application\SacdSelectData;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\application\traits\EncargoFunciones;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\InfoEncargoTipo;
use src\encargossacd\domain\services\EncargoDominioService;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoHorarioRepository;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoRepository;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoSacdHorarioRepository;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoSacdObservRepository;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoSacdRepository;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoTextoRepository;
use src\encargossacd\infrastructure\persistence\postgresql\PgEncargoTipoRepository;
use function DI\autowire;

return [
    EncargoHorarioRepositoryInterface::class => autowire(PgEncargoHorarioRepository::class),
    EncargoSacdObservRepositoryInterface::class => autowire(PgEncargoSacdObservRepository::class),
    EncargoTextoRepositoryInterface::class => autowire(PgEncargoTextoRepository::class),
    EncargoTipoRepositoryInterface::class => autowire(PgEncargoTipoRepository::class),
    EncargoRepositoryInterface::class => autowire(PgEncargoRepository::class),
    EncargoSacdRepositoryInterface::class => autowire(PgEncargoSacdRepository::class),
    EncargoSacdHorarioRepositoryInterface::class => autowire(PgEncargoSacdHorarioRepository::class),

    EncargoAplicacionService::class => autowire(EncargoAplicacionService::class),
    EncargoDominioService::class => autowire(EncargoDominioService::class),
    CentrosPorFiltroOpciones::class => autowire(CentrosPorFiltroOpciones::class),
    EncargoFunciones::class => autowire(EncargoFunciones::class),
    InfoEncargoTipo::class => autowire(InfoEncargoTipo::class),
    CtrFichaData::class => autowire(CtrFichaData::class),
    CtrFichaUpdate::class => autowire(CtrFichaUpdate::class),
    CtrGetFichaData::class => autowire(CtrGetFichaData::class),
    EncargoComprobacionesCtr::class => autowire(EncargoComprobacionesCtr::class),
    EncargoCtrSelectData::class => autowire(EncargoCtrSelectData::class),
    EncargoHorarioSelectData::class => autowire(EncargoHorarioSelectData::class),
    EncargoHorarioUpdate::class => autowire(EncargoHorarioUpdate::class),
    EncargoHorarioVerData::class => autowire(EncargoHorarioVerData::class),
    EncargoLstTipoEncData::class => autowire(EncargoLstTipoEncData::class),
    EncargoSacdHorarioUpdate::class => autowire(EncargoSacdHorarioUpdate::class),
    EncargoSacdHorarioVerData::class => autowire(EncargoSacdHorarioVerData::class),
    EncargoSelectData::class => autowire(EncargoSelectData::class),
    EncargoVerData::class => autowire(EncargoVerData::class),
    EncargoVerEditar::class => autowire(EncargoVerEditar::class),
    EncargoVerEliminar::class => autowire(EncargoVerEliminar::class),
    EncargoVerNuevo::class => autowire(EncargoVerNuevo::class),
    EncargoZonasSelectData::class => autowire(EncargoZonasSelectData::class),
    ListasAData::class => autowire(ListasAData::class),
    ListasBData::class => autowire(ListasBData::class),
    ListasCData::class => autowire(ListasCData::class),
    ListasClData::class => autowire(ListasClData::class),
    ListasComCtrData::class => autowire(ListasComCtrData::class),
    ListasComSacdData::class => autowire(ListasComSacdData::class),
    ListasComTxtData::class => autowire(ListasComTxtData::class),
    ListasComTxtGet::class => autowire(ListasComTxtGet::class),
    ListasComTxtUpdate::class => autowire(ListasComTxtUpdate::class),
    ListasDData::class => autowire(ListasDData::class),
    ListasExigenciaCtrData::class => autowire(ListasExigenciaCtrData::class),
    SacdAusenciasGetData::class => autowire(SacdAusenciasGetData::class),
    SacdAusenciasJefeZonaData::class => autowire(SacdAusenciasJefeZonaData::class),
    SacdAusenciasUpdate::class => autowire(SacdAusenciasUpdate::class),
    SacdFichaData::class => autowire(SacdFichaData::class),
    SacdFichaUpdate::class => autowire(SacdFichaUpdate::class),
    SacdSelectData::class => autowire(SacdSelectData::class),
];
