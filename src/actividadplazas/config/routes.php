<?php

/**
 * Rutas del modulo `actividadplazas`. Las registra `public/index.php`
 * via glob sobre `src/*\/config/routes.php`. Cada endpoint vive en
 * `src/actividadplazas/infrastructure/ui/http/controllers/` y
 * responde JSON mediante `frontend\shared\web\ContestarJson::enviar(...)`, excepto
 * `gestion_plazas_update` que devuelve text/plain para mantener
 * compatibilidad con `frontend\shared\web\TablaEditable`.
 */
return static function ($r) {
    // Lecturas (data builders).
    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/gestion_plazas_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/gestion_plazas_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/posibles_propietarios_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/posibles_propietarios_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/peticiones_activ_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/peticiones_activ_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/plazas_balance_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/plazas_balance_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/resumen_plazas_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/resumen_plazas_data.php';
    });

    // Mutaciones.
    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/gestion_plazas_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/gestion_plazas_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/peticiones_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/peticiones_guardar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/peticiones_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/peticiones_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/peticiones_incorporar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/peticiones_incorporar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadplazas/plazas_ceder', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/plazas_ceder.php';
    });
};
