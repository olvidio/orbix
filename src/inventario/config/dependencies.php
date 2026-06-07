<?php

use src\inventario\application\ColeccionesOpcionesData;
use src\inventario\application\EquipajeEliminar;
use src\inventario\application\InventarioCssInlineData;
use src\inventario\application\TipoDocOpcionesData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\InfoColeccion;
use src\inventario\domain\InfoDocsxCtr;
use src\inventario\domain\InfoDocsxSigla;
use src\inventario\domain\InfoLugar;
use src\inventario\domain\InfoTipoDoc;
use src\inventario\domain\InfoUbiInventario;
use src\inventario\domain\ListaDocsGrupo;
use src\inventario\infrastructure\persistence\postgresql\PgColeccionRepository;
use src\inventario\infrastructure\persistence\postgresql\PgDocumentoRepository;
use src\inventario\infrastructure\persistence\postgresql\PgEgmRepository;
use src\inventario\infrastructure\persistence\postgresql\PgEquipajeRepository;
use src\inventario\infrastructure\persistence\postgresql\PgLugarRepository;
use src\inventario\infrastructure\persistence\postgresql\PgTipoDocRepository;
use src\inventario\infrastructure\persistence\postgresql\PgUbiInventarioRepository;
use src\inventario\infrastructure\persistence\postgresql\PgWhereisRepository;
use function DI\autowire;

return [
    ColeccionRepositoryInterface::class => autowire(PgColeccionRepository::class),
    DocumentoRepositoryInterface::class => autowire(PgDocumentoRepository::class),
    EgmRepositoryInterface::class => autowire(PgEgmRepository::class),
    EquipajeRepositoryInterface::class => autowire(PgEquipajeRepository::class),
    LugarRepositoryInterface::class => autowire(PgLugarRepository::class),
    TipoDocRepositoryInterface::class => autowire(PgTipoDocRepository::class),
    UbiInventarioRepositoryInterface::class => autowire(PgUbiInventarioRepository::class),
    WhereisRepositoryInterface::class => autowire(PgWhereisRepository::class),

    ColeccionesOpcionesData::class => autowire(ColeccionesOpcionesData::class),
    EquipajeEliminar::class => autowire(EquipajeEliminar::class),
    InventarioCssInlineData::class => autowire(InventarioCssInlineData::class),
    TipoDocOpcionesData::class => autowire(TipoDocOpcionesData::class),

    InfoColeccion::class => autowire(InfoColeccion::class),
    InfoDocsxCtr::class => autowire(InfoDocsxCtr::class),
    InfoDocsxSigla::class => autowire(InfoDocsxSigla::class),
    InfoLugar::class => autowire(InfoLugar::class),
    InfoTipoDoc::class => autowire(InfoTipoDoc::class),
    InfoUbiInventario::class => autowire(InfoUbiInventario::class),
    ListaDocsGrupo::class => autowire(ListaDocsGrupo::class),
];
