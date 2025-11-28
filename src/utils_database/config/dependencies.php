<?php

use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\infrastructure\repositories\PgAnuncioRepository;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\infrastructure\repositories\PgDbSchemaRepository;
use src\utils_database\infrastructure\repositories\PgMapIdRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    // 'autowire()' le dice a PHP-DI: "Intenta inyectar el PDO automáticamente en el constructor de Pg...Repository"
    DbSchemaRepositoryInterface::class => autowire(PgDbSchemaRepository::class),
    MapIdRepositoryInterface::class => autowire(PgMapIdRepository::class),
];