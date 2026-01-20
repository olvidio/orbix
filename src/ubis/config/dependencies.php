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
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\infrastructure\repositories\PgCasaDlRepository;
use src\ubis\infrastructure\repositories\PgCasaExRepository;
use src\ubis\infrastructure\repositories\PgCasaRepository;
use src\ubis\infrastructure\repositories\PgCentroDlRepository;
use src\ubis\infrastructure\repositories\PgCentroEllasRepository;
use src\ubis\infrastructure\repositories\PgCentroEllosRepository;
use src\ubis\infrastructure\repositories\PgCentroExRepository;
use src\ubis\infrastructure\repositories\PgCentroRepository;
use src\ubis\infrastructure\repositories\PgDelegacionRepository;
use src\ubis\infrastructure\repositories\PgDescTelecoRepository;
use src\ubis\infrastructure\repositories\PgDireccionCasaDlRepository;
use src\ubis\infrastructure\repositories\PgDireccionCasaExRepository;
use src\ubis\infrastructure\repositories\PgDireccionCasaRepository;
use src\ubis\infrastructure\repositories\PgDireccionCentroDlRepository;
use src\ubis\infrastructure\repositories\PgDireccionCentroExRepository;
use src\ubis\infrastructure\repositories\PgDireccionCentroRepository;
use src\ubis\infrastructure\repositories\PgDireccionRepository;
use src\ubis\infrastructure\repositories\PgRegionRepository;
use src\ubis\infrastructure\repositories\PgRelacionCasaDireccionRepository;
use src\ubis\infrastructure\repositories\PgRelacionCasaDlDireccionRepository;
use src\ubis\infrastructure\repositories\PgRelacionCasaExDireccionRepository;
use src\ubis\infrastructure\repositories\PgRelacionCentroDireccionRepository;
use src\ubis\infrastructure\repositories\PgRelacionCentroDlDireccionRepository;
use src\ubis\infrastructure\repositories\PgRelacionCentroExDireccionRepository;
use src\ubis\infrastructure\repositories\PgRelacionUbiDireccionRepository;
use src\ubis\infrastructure\repositories\PgTelecoCdcDlRepository;
use src\ubis\infrastructure\repositories\PgTelecoCdcExRepository;
use src\ubis\infrastructure\repositories\PgTelecoCdcRepository;
use src\ubis\infrastructure\repositories\PgTelecoCtrDlRepository;
use src\ubis\infrastructure\repositories\PgTelecoCtrExRepository;
use src\ubis\infrastructure\repositories\PgTelecoCtrRepository;
use src\ubis\infrastructure\repositories\PgTipoCasaRepository;
use src\ubis\infrastructure\repositories\PgTipoCentroRepository;
use src\ubis\infrastructure\repositories\PgTipoTelecoRepository;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\infrastructure\repositories\PgCasaPeriodoRepository;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\infrastructure\repositories\PgTarifaUbiRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    // 'autowire()' le dice a PHP-DI: "Intenta inyectar el PDO automáticamente en el constructor de Pg...Repository"
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
    TipoCasaRepositoryInterface::class => autowire(PgTipoCasaRepository::class),
    TipoCentroRepositoryInterface::class => autowire(PgTipoCentroRepository::class),
    TipoTelecoRepositoryInterface::class => autowire(PgTipoTelecoRepository::class),
    CasaPeriodoRepositoryInterface::class => autowire(PgCasaPeriodoRepository::class),
    TarifaUbiRepositoryInterface::class => autowire(PgTarifaUbiRepository::class),
];