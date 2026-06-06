<?php

use src\actividadescentro\application\ActivCtrShellData;
use src\actividadescentro\application\CentroEncargadoAsignar;
use src\actividadescentro\application\CentroEncargadoEliminar;
use src\actividadescentro\application\CentroEncargadoReordenar;
use src\actividadescentro\application\CentrosDisponiblesData;
use src\actividadescentro\application\CentrosEncargadosData;
use src\actividadescentro\application\ListaActividadesCtrData;
use src\actividadescentro\domain\Info3010;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\infrastructure\persistence\postgresql\PgCentroEncargadoRepository;
use function DI\autowire;

return [
    CentroEncargadoRepositoryInterface::class => autowire(PgCentroEncargadoRepository::class),

    ActivCtrShellData::class => autowire(ActivCtrShellData::class),
    CentroEncargadoAsignar::class => autowire(CentroEncargadoAsignar::class),
    CentroEncargadoEliminar::class => autowire(CentroEncargadoEliminar::class),
    CentroEncargadoReordenar::class => autowire(CentroEncargadoReordenar::class),
    CentrosDisponiblesData::class => autowire(CentrosDisponiblesData::class),
    CentrosEncargadosData::class => autowire(CentrosEncargadosData::class),
    ListaActividadesCtrData::class => autowire(ListaActividadesCtrData::class),
    Info3010::class => autowire(Info3010::class),
];
