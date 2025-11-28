<?php

use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\infrastructure\repositories\PgAnuncioRepository;
use function DI\autowire;

return [
    // Mapeo simple: Interfaz => Clase
    // 'autowire()' le dice a PHP-DI: "Intenta inyectar el PDO automáticamente en el constructor de Pg...Repository"
    AnuncioRepositoryInterface::class => autowire(PgAnuncioRepository::class),
];