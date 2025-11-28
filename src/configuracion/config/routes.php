<?php
// Rutas del módulo Configuracion
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/configuracion/infrastructure/controllers/*.php
    // se mapea a la ruta /configuracion/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/configuracion/parametros_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/parametros_lista.php';
    });
    $r->addRoute(['GET','POST'], '/configuracion/parametros_update', function () {
        require __DIR__ . '/../infrastructure/controllers/parametros_update.php';
    });
};
