<?php
// Rutas del módulo Procesos
// Convención: cada archivo en src/procesos/infrastructure/ui/http/controllers/*.php
// se mapea a la ruta /src/procesos/<nombre_archivo_sin_php>
// Permitimos GET y POST para máxima compatibilidad durante la migración.

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_ver_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_ver_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/tipo_activ_proceso_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_proceso_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_proceso_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_proceso_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_proceso_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_proceso_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_que_fases_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_que_fases_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/fases_activ_cambio_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/fases_activ_cambio_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/usuario_perm_activ_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php';
    });
};
