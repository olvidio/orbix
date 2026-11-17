<?php

declare(strict_types=1);

// Rutas HTTP del módulo devel_codegen: el generador (frontend/devel_codegen) pide aquí la estructura de la tabla.
return static function ($r): void {
    $r->addRoute(['GET', 'POST'], '/src/devel_codegen/tabla_estructura_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tabla_estructura_datos.php';
    });
};
