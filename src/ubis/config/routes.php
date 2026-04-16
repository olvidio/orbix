<?php
// Rutas del módulo Ubis
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/ubis/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /ubis/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_tabla', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_tabla.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_editar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_editar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_desc_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_desc_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_que', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_que.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_tabla', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_tabla.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_editar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_editar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_asignar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_asignar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_quitar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_quitar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direccion_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direccion_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/delegacion_que_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/delegacion_que_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/trasladar_ubis', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/trasladar_ubis.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/calendario_periodos_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_periodos_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/calendario_periodos_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_periodos_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/calendario_periodos_nuevo_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_periodos_nuevo_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/calendario_periodos_form_periodo_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_periodos_form_periodo_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/calendario_periodos_get2_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_periodos_get2_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/calendario_periodos_get_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_periodos_get_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_get_labor', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_get_labor.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_get_num', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_get_num.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_get_plazas', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_get_plazas.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_form_labor', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_form_labor.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_form_num', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_form_num.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_form_plazas', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_form_plazas.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/centros_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/home_ubis_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/home_ubis_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_tabla_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_tabla_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_buscar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_buscar_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_editar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_editar_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/list_ctr_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/list_ctr_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/lista_ctrs_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_ctrs_data.php';
    });

};
