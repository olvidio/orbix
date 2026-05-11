<?php

/**
 * Rutas del módulo `casas`. Las registra `public/index.php` via glob
 * sobre `src/*\/config/routes.php`. Cada endpoint vive en
 * `src/casas/infrastructure/ui/http/controllers/` y responde JSON
 * estándar mediante `src\shared\web\ContestarJson::enviar(...)`.
 */
return static function ($r) {
    // Grupos de casas (padre ↔ hijo).
    $r->addRoute(['GET', 'POST'], '/src/casas/grupo_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/grupo_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/grupo_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/grupo_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/grupo_eliminar.php';
    });

    // Previsión de asistentes (plazas previstas por actividad).
    $r->addRoute(['GET', 'POST'], '/src/casas/prevision_asistentes_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/prevision_asistentes_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/ingreso_plazas_previstas_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ingreso_plazas_previstas_update.php';
    });

    // Estudio económico y de ocupación de una casa.
    $r->addRoute(['GET', 'POST'], '/src/casas/calendario_ubi_resumen_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php';
    });

    // Actividades e ingresos por casa (pantalla `casa`).
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_ingresos_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_ingresos_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_actividades_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_actividades_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_ingreso_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_ingreso_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_ingreso_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_ingreso_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_ingreso_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_ingreso_eliminar.php';
    });

    // Resumen económico global (pantalla `casa_resumen` y `casa_ec`).
    $r->addRoute(['GET', 'POST'], '/src/casas/casas_resumen_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casas_resumen_data.php';
    });

    // Gastos y aportaciones anuales por casa (pantalla `casa_ec`).
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_ec_gastos_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_ec_gastos_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/casas/casa_ec_gastos_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php';
    });
};
