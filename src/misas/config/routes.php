<?php

// Rutas del modulo `misas`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/misas/infrastructure/ui/http/controllers/` y responde JSON mediante
// `frontend\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    // Slice 3: Iniciales SACD por zona.
    $r->addRoute(['GET', 'POST'], '/src/misas/modificar_iniciales_sacd_zona_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/ver_iniciales_zona_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_iniciales_zona_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/update_iniciales', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/update_iniciales.php';
    });

    // Slice 4: Encargos zona.
    $r->addRoute(['GET', 'POST'], '/src/misas/modificar_encargos_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modificar_encargos_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/ver_encargos_zona_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_encargos_zona_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/guardar_encargo_zona', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/guardar_encargo_zona.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/eliminar_encargo_zona', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/eliminar_encargo_zona.php';
    });

    // Slice 5: Encargos por centro.
    $r->addRoute(['GET', 'POST'], '/src/misas/modificar_encargos_centros_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modificar_encargos_centros_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/ver_encargos_centros_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_encargos_centros_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/guardar_encargo_centro', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/guardar_encargo_centro.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/eliminar_encargo_centro', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/eliminar_encargo_centro.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/desplegable_encargos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/desplegable_encargos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/desplegable_sacd', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/desplegable_sacd.php';
    });

    // Slice 7: Plan de misas de un sacerdote.
    $r->addRoute(['GET', 'POST'], '/src/misas/buscar_plan_sacd_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/buscar_plan_sacd_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/ver_plan_sacd_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_plan_sacd_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/buscar_plan_ctr_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/buscar_plan_ctr_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/ver_plan_ctr_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_plan_ctr_data.php';
    });

    // Slice 8 — Preparar / modificar / ver plan de misas (formulario).
    $r->addRoute(['GET', 'POST'], '/src/misas/plan_de_misas_pantalla_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php';
    });

    // Slice 9 — Modificar plantilla (datos formulario) + horarios / plantilla CTR.
    $r->addRoute(['GET', 'POST'], '/src/misas/modificar_plantilla_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/modificar_plantilla_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/guardar_horario', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/guardar_horario.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/quitar_horario', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/quitar_horario.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/anadir_ctr_tarea', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/anadir_ctr_tarea.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/horario_tarea_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/horario_tarea_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/nuevo_status', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/nuevo_status.php';
    });

    // Slice 10 — Cambiar estado + datos zona/sacd (JSON).
    $r->addRoute(['GET', 'POST'], '/src/misas/cambiar_status_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cambiar_status_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/zona_sacd_datos_get', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd_datos_get.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/misas/zona_sacd_datos_put', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd_datos_put.php';
    });

    // Slice 6a: Cuadricula update (mutacion de `EncargoDia` por celda del grid).
    $r->addRoute(['GET', 'POST'], '/src/misas/cuadricula_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cuadricula_update.php';
    });

    // Slice 6b: Cuadricula zona (payload SlickGrid).
    $r->addRoute(['GET', 'POST'], '/src/misas/ver_cuadricula_zona_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php';
    });

    // Slice 8 (revision): Crear nuevo periodo (mutaciones EncargoDia + payload SlickGrid).
    $r->addRoute(['GET', 'POST'], '/src/misas/crear_nuevo_periodo_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php';
    });

    // Slice 9 (revision): Importar plantilla (mutacion masiva EncargoDia).
    $r->addRoute(['GET', 'POST'], '/src/misas/importar_plantilla_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/importar_plantilla_data.php';
    });

    // Slice 10 (revision): Ver misas zona (payload SlickGrid read-only).
    $r->addRoute(['GET', 'POST'], '/src/misas/ver_misas_zona_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_misas_zona_data.php';
    });
};
