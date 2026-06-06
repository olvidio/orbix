<?php

use src\casas\application\CalendarioUbiResumenData;
use src\casas\application\CasaActividadesListaData;
use src\casas\application\CasaEcGastosFormData;
use src\casas\application\CasaEcGastosGuardar;
use src\casas\application\CasaIngresoEliminar;
use src\casas\application\CasaIngresoFormData;
use src\casas\application\CasaIngresosListaData;
use src\casas\application\CasaIngresoUpdate;
use src\casas\application\CasasResumenData;
use src\casas\application\GrupoCasaEliminar;
use src\casas\application\GrupoCasaFormData;
use src\casas\application\GrupoCasaListaData;
use src\casas\application\GrupoCasaUpdate;
use src\casas\application\IngresoPlazasPrevistasUpdate;
use src\casas\application\PrevisionAsistentesData;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\infrastructure\persistence\postgresql\PgGrupoCasaRepository;
use src\casas\infrastructure\persistence\postgresql\PgIngresoRepository;
use src\casas\infrastructure\persistence\postgresql\PgUbiGastoRepository;
use function DI\autowire;

return [
    // Mapeos de Interfaces a Implementaciones
    IngresoRepositoryInterface::class => autowire(PgIngresoRepository::class),
    UbiGastoRepositoryInterface::class => autowire(PgUbiGastoRepository::class),
    GrupoCasaRepositoryInterface::class => autowire(PgGrupoCasaRepository::class),

    // Casos de uso / Application classes
    CalendarioUbiResumenData::class => autowire(CalendarioUbiResumenData::class),
    CasaActividadesListaData::class => autowire(CasaActividadesListaData::class),
    CasaEcGastosFormData::class => autowire(CasaEcGastosFormData::class),
    CasaEcGastosGuardar::class => autowire(CasaEcGastosGuardar::class),
    CasaIngresoEliminar::class => autowire(CasaIngresoEliminar::class),
    CasaIngresoFormData::class => autowire(CasaIngresoFormData::class),
    CasaIngresosListaData::class => autowire(CasaIngresosListaData::class),
    CasaIngresoUpdate::class => autowire(CasaIngresoUpdate::class),
    CasasResumenData::class => autowire(CasasResumenData::class),
    GrupoCasaEliminar::class => autowire(GrupoCasaEliminar::class),
    GrupoCasaFormData::class => autowire(GrupoCasaFormData::class),
    GrupoCasaListaData::class => autowire(GrupoCasaListaData::class),
    GrupoCasaUpdate::class => autowire(GrupoCasaUpdate::class),
    IngresoPlazasPrevistasUpdate::class => autowire(IngresoPlazasPrevistasUpdate::class),
    PrevisionAsistentesData::class => autowire(PrevisionAsistentesData::class),
];
