<?php

/**
 * Rutas del modulo `actividadtarifas`. Las registra `public/index.php`
 * via glob sobre `src/*\/config/routes.php`. Cada endpoint vive en
 * `src/actividadtarifas/infrastructure/ui/http/controllers/` y
 * responde JSON estandar mediante `src\shared\web\ContestarJson::enviar(...)`.
 */
return static function ($r) {
    // Tipos de tarifa (catalogo).
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tipo_tarifa_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tipo_tarifa_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_tarifa_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tipo_tarifa_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_tarifa_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tipo_tarifa_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php';
    });

    // Tarifas por casa y año.
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tarifa_ubi_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tarifa_ubi_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tarifa_ubi_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tarifa_ubi_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tarifa_ubi_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tarifa_ubi_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tarifa_ubi_update_inc', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/tarifa_ubi_copiar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tarifa_ubi_copiar.php';
    });

    // Relacion tarifa ↔ tipo actividad.
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/relacion_tarifa_lista_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/relacion_tarifa_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/relacion_tarifa_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/relacion_tarifa_form_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/relacion_tarifa_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/relacion_tarifa_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadtarifas/relacion_tarifa_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php';
    });
};
