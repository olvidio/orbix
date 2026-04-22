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

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_regenerar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_regenerar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_clonar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_clonar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_get', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_get.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_get_listado', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_get_listado.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_depende', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_depende.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/procesos_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/procesos_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/tipo_activ_proceso_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_proceso_lista.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/tipo_activ_proceso_lst_posibles', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_proceso_lst_posibles.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/tipo_activ_proceso_asignar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_proceso_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_proceso_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_proceso_generar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_proceso_generar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_proceso_get', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_proceso_get.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_proceso_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_proceso_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/actividad_que_fases_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_que_fases_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/fases_activ_cambio_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/fases_activ_cambio_lista.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/fases_activ_cambio_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/fases_activ_cambio_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/fases_activ_cambio_get', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/fases_activ_cambio_get.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/usuario_perm_activ_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_perm_activ_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/procesos/usuario_perm_activ_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php';
    });
};
