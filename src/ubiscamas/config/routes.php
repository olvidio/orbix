<?php
// Rutas del módulo Usuarios
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/usuarios/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /usuarios/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/cama_delete', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cama_delete.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/cama_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cama_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/habitacion_delete', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/habitacion_delete.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/habitacion_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/habitacion_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/update_cama_asistente', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/update_cama_asistente.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/actividad_habitaciones_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_habitaciones_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/update_solo_vip', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/update_solo_vip.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/habitacion_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/habitacion_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubiscamas/cama_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cama_form_data.php';
    });
};