<?php

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
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
    // Mapeos de Interfaces a Implementaciones
    ColeccionRepositoryInterface::class => autowire(PgColeccionRepository::class),
    DocumentoRepositoryInterface::class => autowire(PgDocumentoRepository::class),
    EgmRepositoryInterface::class => autowire(PgEgmRepository::class),
    EquipajeRepositoryInterface::class => autowire(PgEquipajeRepository::class),
    LugarRepositoryInterface::class => autowire(PgLugarRepository::class),
    TipoDocRepositoryInterface::class => autowire(PgTipoDocRepository::class),
    UbiInventarioRepositoryInterface::class => autowire(PgUbiInventarioRepository::class),
    WhereisRepositoryInterface::class => autowire(PgWhereisRepository::class),
];
