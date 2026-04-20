<?php
// Rutas del módulo Actividades
// Convención: cada archivo en src/actividades/infrastructure/ui/http/controllers/*.php
// se mapea a la ruta /src/actividades/<nombre_archivo_sin_php>
// Permitimos GET y POST para máxima compatibilidad durante la migración.

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/actividades/tipo_activ_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_lista.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/tipo_activ_form_nuevo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_form_nuevo.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/tipo_activ_form_modificar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_form_modificar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/tipo_activ_nuevo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_nuevo.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/tipo_activ_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/tipo_activ_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_tipo_get', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_tipo_get.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_que_filtros', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_que_filtros.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/lista_activ', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_activ.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_select_ubi_desplegable', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_ver_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_ver_datos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_select_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_select_datos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/lista_actividades_sg_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_actividades_sg_datos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/lista_centros_activ_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_centros_activ_datos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/actividad_nuevo_curso_ejecutar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/lista_sr_csv_que_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_sr_csv_que_datos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/lista_sr_csv_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_sr_csv_datos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividades/calendario_listas_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_listas_datos.php';
    });
};
