<?php
// Rutas del módulo Configuracion
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/configuracion/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /configuracion/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/src/configuracion/parametros_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/parametros_lista.php';
    });
    $r->addRoute(['GET','POST'], '/src/configuracion/parametros_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/parametros_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/configuracion/periodo_calendario_escolar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/periodo_calendario_escolar_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/configuracion/modulos_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modulos_select_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/configuracion/modulos_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modulos_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/configuracion/modulos_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modulos_update.php';
    });
};
