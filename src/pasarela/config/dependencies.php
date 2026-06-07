<?php

use src\pasarela\application\ActivacionDefaultData;
use src\pasarela\application\ActivacionDefaultGuardar;
use src\pasarela\application\ActivacionExcepcionEliminar;
use src\pasarela\application\ActivacionExcepcionGuardar;
use src\pasarela\application\ActivacionLista;
use src\pasarela\application\ContribucionNoDuermeDefaultData;
use src\pasarela\application\ContribucionNoDuermeDefaultGuardar;
use src\pasarela\application\ContribucionNoDuermeExcepcionEliminar;
use src\pasarela\application\ContribucionNoDuermeExcepcionGuardar;
use src\pasarela\application\ContribucionNoDuermeLista;
use src\pasarela\application\ContribucionReservaDefaultData;
use src\pasarela\application\ContribucionReservaDefaultGuardar;
use src\pasarela\application\ContribucionReservaExcepcionEliminar;
use src\pasarela\application\ContribucionReservaExcepcionGuardar;
use src\pasarela\application\ContribucionReservaLista;
use src\pasarela\application\Conversiones;
use src\pasarela\application\ExportarActividadesData;
use src\pasarela\application\ExportarQueActividadTipoHtml;
use src\pasarela\application\NombreExcepcionEliminar;
use src\pasarela\application\NombreExcepcionGuardar;
use src\pasarela\application\NombreLista;
use src\pasarela\application\TipoActivTxtData;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\ContribucionNoDuerme;
use src\pasarela\domain\ContribucionReserva;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\Nombre;
use src\pasarela\infrastructure\persistence\postgresql\PgPasarelaConfigRepository;
use function DI\autowire;

return [
    PasarelaConfigRepositoryInterface::class => autowire(PgPasarelaConfigRepository::class),

    Activacion::class => autowire(Activacion::class),
    Nombre::class => autowire(Nombre::class),
    ContribucionReserva::class => autowire(ContribucionReserva::class),
    ContribucionNoDuerme::class => autowire(ContribucionNoDuerme::class),

    Conversiones::class => autowire(Conversiones::class),

    ActivacionLista::class => autowire(ActivacionLista::class),
    ActivacionDefaultData::class => autowire(ActivacionDefaultData::class),
    ActivacionDefaultGuardar::class => autowire(ActivacionDefaultGuardar::class),
    ActivacionExcepcionGuardar::class => autowire(ActivacionExcepcionGuardar::class),
    ActivacionExcepcionEliminar::class => autowire(ActivacionExcepcionEliminar::class),

    NombreLista::class => autowire(NombreLista::class),
    NombreExcepcionGuardar::class => autowire(NombreExcepcionGuardar::class),
    NombreExcepcionEliminar::class => autowire(NombreExcepcionEliminar::class),

    ContribucionReservaLista::class => autowire(ContribucionReservaLista::class),
    ContribucionReservaDefaultData::class => autowire(ContribucionReservaDefaultData::class),
    ContribucionReservaDefaultGuardar::class => autowire(ContribucionReservaDefaultGuardar::class),
    ContribucionReservaExcepcionGuardar::class => autowire(ContribucionReservaExcepcionGuardar::class),
    ContribucionReservaExcepcionEliminar::class => autowire(ContribucionReservaExcepcionEliminar::class),

    ContribucionNoDuermeLista::class => autowire(ContribucionNoDuermeLista::class),
    ContribucionNoDuermeDefaultData::class => autowire(ContribucionNoDuermeDefaultData::class),
    ContribucionNoDuermeDefaultGuardar::class => autowire(ContribucionNoDuermeDefaultGuardar::class),
    ContribucionNoDuermeExcepcionGuardar::class => autowire(ContribucionNoDuermeExcepcionGuardar::class),
    ContribucionNoDuermeExcepcionEliminar::class => autowire(ContribucionNoDuermeExcepcionEliminar::class),

    ExportarActividadesData::class => autowire(ExportarActividadesData::class),
    ExportarQueActividadTipoHtml::class => autowire(ExportarQueActividadTipoHtml::class),
    TipoActivTxtData::class => autowire(TipoActivTxtData::class),
];
