<?php

use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionRepositoryInterface;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionUbiDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\infrastructure\persistence\postgresql\PgCasaDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCasaExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCasaRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCentroDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCentroEllasRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCentroEllosRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCentroExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgCentroRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDelegacionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDescTelecoRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCasaRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionCentroRepository;
use src\ubis\infrastructure\persistence\postgresql\PgDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRegionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionCasaDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionCasaDlDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionCasaExDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionCentroDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionCentroDlDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionCentroExDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgRelacionUbiDireccionRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTelecoCdcDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTelecoCdcExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTelecoCdcRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTelecoCtrDlRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTelecoCtrExRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTelecoCtrRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTipoCasaRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTipoCentroRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTipoTelecoRepository;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\infrastructure\persistence\postgresql\PgCasaPeriodoRepository;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;
use src\ubis\infrastructure\persistence\postgresql\PgTarifaUbiRepository;
use src\ubis\infrastructure\persistence\postgresql\PgTrasladoUbiRepository;
use src\ubis\application\CalendarioPeriodoEliminar;
use src\ubis\application\CalendarioPeriodoGuardar;
use src\ubis\application\CalendarioPeriodosFormPeriodoData;
use src\ubis\application\CalendarioPeriodosGet2Data;
use src\ubis\application\CalendarioPeriodosGetData;
use src\ubis\application\CalendarioPeriodosNuevoData;
use src\ubis\application\CasasOpcionesData;
use src\ubis\application\CentrosFormData;
use src\ubis\application\CentrosGetLaborData;
use src\ubis\application\CentrosGetNumData;
use src\ubis\application\CentrosGetPlazasData;
use src\ubis\application\CentrosOpcionesData;
use src\ubis\application\CentrosSListaData;
use src\ubis\application\CentrosUpdate;
use src\ubis\application\DelegacionQueData;
use src\ubis\application\DelegacionesRegionStgrData;
use src\ubis\application\DireccionUpdate;
use src\ubis\application\DireccionesAsignar;
use src\ubis\application\DireccionesEditarData;
use src\ubis\application\DireccionesQueData;
use src\ubis\application\DireccionesQuitar;
use src\ubis\application\DireccionesResolver;
use src\ubis\application\DireccionesTablaData;
use src\ubis\application\HomeUbisData;
use src\ubis\application\ListCtrData;
use src\ubis\application\TelecoDescLista;
use src\ubis\application\TelecoEditarData;
use src\ubis\application\TelecoEliminar;
use src\ubis\application\TelecoGuardar;
use src\ubis\application\TelecoTablaData;
use src\ubis\application\TrasladarUbis;
use src\ubis\application\UbiFactory;
use src\ubis\application\UbisBuscarOpcionesData;
use src\ubis\application\UbisEditarLoadData;
use src\ubis\application\UbisEditarNormalizeDlData;
use src\ubis\application\UbisEditarOpcionesData;
use src\ubis\application\UbisEliminar;
use src\ubis\application\UbisGuardar;
use src\ubis\application\UbisListaData;
use src\ubis\application\UbisTablaData;
use src\ubis\application\UbisTiposLaborEtiquetas;
use src\ubis\application\services\CasasDropdown;
use src\ubis\application\services\CentrosDropdown;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\DelegacionQuery;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\application\services\RegionDropdown;
use src\ubis\application\services\TipoCasaDropdown;
use src\ubis\application\services\TipoCentroDropdown;
use src\ubis\application\services\UbiPermisos;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\application\services\UbiTelecoService;
use src\ubis\domain\InfoDelegaciones;
use src\ubis\domain\InfoDescTeleco;
use src\ubis\domain\InfoRegiones;
use src\ubis\domain\InfoTelecoUbi;
use src\ubis\domain\InfoTipoCasa;
use src\ubis\domain\InfoTipoCtr;
use src\ubis\domain\InfoTipoTeleco;

use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    CasaDlRepositoryInterface::class => autowire(PgCasaDlRepository::class),
    CasaExRepositoryInterface::class => autowire(PgCasaExRepository::class),
    CasaRepositoryInterface::class => autowire(PgCasaRepository::class),
    CentroDlRepositoryInterface::class => autowire(PgCentroDlRepository::class),
    CentroEllasRepositoryInterface::class => autowire(PgCentroEllasRepository::class),
    CentroEllosRepositoryInterface::class => autowire(PgCentroEllosRepository::class),
    CentroExRepositoryInterface::class => autowire(PgCentroExRepository::class),
    CentroRepositoryInterface::class => autowire(PgCentroRepository::class),
    DelegacionRepositoryInterface::class => autowire(PgDelegacionRepository::class),
    DescTelecoRepositoryInterface::class => autowire(PgDescTelecoRepository::class),
    DireccionCasaDlRepositoryInterface::class => autowire(PgDireccionCasaDlRepository::class),
    DireccionCasaExRepositoryInterface::class => autowire(PgDireccionCasaExRepository::class),
    DireccionCasaRepositoryInterface::class => autowire(PgDireccionCasaRepository::class),
    DireccionCentroDlRepositoryInterface::class => autowire(PgDireccionCentroDlRepository::class),
    DireccionCentroExRepositoryInterface::class => autowire(PgDireccionCentroExRepository::class),
    DireccionCentroRepositoryInterface::class => autowire(PgDireccionCentroRepository::class),
    DireccionRepositoryInterface::class => autowire(PgDireccionRepository::class),
    RegionRepositoryInterface::class => autowire(PgRegionRepository::class),
    RelacionCasaDireccionRepositoryInterface::class => autowire(PgRelacionCasaDireccionRepository::class),
    RelacionCasaDlDireccionRepositoryInterface::class => autowire(PgRelacionCasaDlDireccionRepository::class),
    RelacionCasaExDireccionRepositoryInterface::class => autowire(PgRelacionCasaExDireccionRepository::class),
    RelacionUbiDireccionRepositoryInterface::class => autowire(PgRelacionUbiDireccionRepository::class),
    RelacionCentroDireccionRepositoryInterface::class => autowire(PgRelacionCentroDireccionRepository::class),
    RelacionCentroDlDireccionRepositoryInterface::class => autowire(PgRelacionCentroDlDireccionRepository::class),
    RelacionCentroExDireccionRepositoryInterface::class => autowire(PgRelacionCentroExDireccionRepository::class),
    TelecoCdcDlRepositoryInterface::class => autowire(PgTelecoCdcDlRepository::class),
    TelecoCdcExRepositoryInterface::class => autowire(PgTelecoCdcExRepository::class),
    TelecoCdcRepositoryInterface::class => autowire(PgTelecoCdcRepository::class),
    TelecoCtrDlRepositoryInterface::class => autowire(PgTelecoCtrDlRepository::class),
    TelecoCtrExRepositoryInterface::class => autowire(PgTelecoCtrExRepository::class),
    TelecoCtrRepositoryInterface::class => autowire(PgTelecoCtrRepository::class),
    TelecoUbiRepositoryInterface::class => autowire(PgTelecoCdcRepository::class),
    TipoCasaRepositoryInterface::class => autowire(PgTipoCasaRepository::class),
    TipoCentroRepositoryInterface::class => autowire(PgTipoCentroRepository::class),
    TipoTelecoRepositoryInterface::class => autowire(PgTipoTelecoRepository::class),
    CasaPeriodoRepositoryInterface::class => autowire(PgCasaPeriodoRepository::class),
    TarifaUbiRepositoryInterface::class => autowire(PgTarifaUbiRepository::class),
    TrasladoUbiRepositoryInterface::class => autowire(PgTrasladoUbiRepository::class),

    // Servicios
    src\ubis\application\services\CasasDropdown::class => autowire(CasasDropdown::class),
    src\ubis\application\services\CentrosDropdown::class => autowire(CentrosDropdown::class),
    src\ubis\application\services\DelegacionDropdown::class => autowire(DelegacionDropdown::class),
    src\ubis\application\services\DelegacionQuery::class => autowire(DelegacionQuery::class),
    src\ubis\application\services\RegionDropdown::class => autowire(RegionDropdown::class),
    src\ubis\application\services\TipoCasaDropdown::class => autowire(TipoCasaDropdown::class),
    src\ubis\application\services\TipoCentroDropdown::class => autowire(TipoCentroDropdown::class),
    src\ubis\application\services\UbiPermisos::class => autowire(UbiPermisos::class),
    src\ubis\application\services\UbiRepositoryResolver::class => autowire(UbiRepositoryResolver::class),
    src\ubis\application\services\UbiTelecoService::class => autowire(UbiTelecoService::class),

    // Info*
    src\ubis\domain\InfoDelegaciones::class => autowire(InfoDelegaciones::class),
    src\ubis\domain\InfoDescTeleco::class => autowire(InfoDescTeleco::class),
    src\ubis\domain\InfoRegiones::class => autowire(InfoRegiones::class),
    src\ubis\domain\InfoTelecoUbi::class => autowire(InfoTelecoUbi::class),
    src\ubis\domain\InfoTipoCasa::class => autowire(InfoTipoCasa::class),
    src\ubis\domain\InfoTipoCtr::class => autowire(InfoTipoCtr::class),
    src\ubis\domain\InfoTipoTeleco::class => autowire(InfoTipoTeleco::class),

    // Casos de uso / Application
    src\ubis\application\CalendarioPeriodoEliminar::class => autowire(CalendarioPeriodoEliminar::class),
    src\ubis\application\CalendarioPeriodoGuardar::class => autowire(CalendarioPeriodoGuardar::class),
    src\ubis\application\CalendarioPeriodosFormPeriodoData::class => autowire(CalendarioPeriodosFormPeriodoData::class),
    src\ubis\application\CalendarioPeriodosGet2Data::class => autowire(CalendarioPeriodosGet2Data::class),
    src\ubis\application\CalendarioPeriodosGetData::class => autowire(CalendarioPeriodosGetData::class),
    src\ubis\application\CalendarioPeriodosNuevoData::class => autowire(CalendarioPeriodosNuevoData::class),
    src\ubis\application\CasasOpcionesData::class => autowire(CasasOpcionesData::class),
    src\ubis\application\CentrosFormData::class => autowire(CentrosFormData::class),
    src\ubis\application\CentrosGetLaborData::class => autowire(CentrosGetLaborData::class),
    src\ubis\application\CentrosGetNumData::class => autowire(CentrosGetNumData::class),
    src\ubis\application\CentrosGetPlazasData::class => autowire(CentrosGetPlazasData::class),
    src\ubis\application\CentrosOpcionesData::class => autowire(CentrosOpcionesData::class),
    src\ubis\application\CentrosSListaData::class => autowire(CentrosSListaData::class),
    src\ubis\application\CentrosUpdate::class => autowire(CentrosUpdate::class),
    src\ubis\application\DelegacionQueData::class => autowire(DelegacionQueData::class),
    src\ubis\application\DelegacionesRegionStgrData::class => autowire(DelegacionesRegionStgrData::class),
    src\ubis\application\DireccionUpdate::class => autowire(DireccionUpdate::class),
    src\ubis\application\DireccionesAsignar::class => autowire(DireccionesAsignar::class),
    src\ubis\application\DireccionesEditarData::class => autowire(DireccionesEditarData::class),
    src\ubis\application\DireccionesQueData::class => autowire(DireccionesQueData::class),
    src\ubis\application\DireccionesQuitar::class => autowire(DireccionesQuitar::class),
    src\ubis\application\DireccionesResolver::class => autowire(DireccionesResolver::class),
    src\ubis\application\DireccionesTablaData::class => autowire(DireccionesTablaData::class),
    src\ubis\application\HomeUbisData::class => autowire(HomeUbisData::class),
    src\ubis\application\ListCtrData::class => autowire(ListCtrData::class),
    src\ubis\application\TelecoDescLista::class => autowire(TelecoDescLista::class),
    src\ubis\application\TelecoEditarData::class => autowire(TelecoEditarData::class),
    src\ubis\application\TelecoEliminar::class => autowire(TelecoEliminar::class),
    src\ubis\application\TelecoGuardar::class => autowire(TelecoGuardar::class),
    src\ubis\application\TelecoTablaData::class => autowire(TelecoTablaData::class),
    src\ubis\application\TrasladarUbis::class => autowire(TrasladarUbis::class),
    src\ubis\application\UbiFactory::class => autowire(UbiFactory::class),
    src\ubis\application\UbisBuscarOpcionesData::class => autowire(UbisBuscarOpcionesData::class),
    src\ubis\application\UbisEditarLoadData::class => autowire(UbisEditarLoadData::class),
    src\ubis\application\UbisEditarNormalizeDlData::class => autowire(UbisEditarNormalizeDlData::class),
    src\ubis\application\UbisEditarOpcionesData::class => autowire(UbisEditarOpcionesData::class),
    src\ubis\application\UbisEliminar::class => autowire(UbisEliminar::class),
    src\ubis\application\UbisGuardar::class => autowire(UbisGuardar::class),
    src\ubis\application\UbisListaData::class => autowire(UbisListaData::class),
    src\ubis\application\UbisTablaData::class => autowire(UbisTablaData::class),
    src\ubis\application\UbisTiposLaborEtiquetas::class => autowire(UbisTiposLaborEtiquetas::class),
];
