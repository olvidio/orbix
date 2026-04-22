<?php

/**
 * Rutas del modulo `personas`.
 *
 * Convencion: cada fichero en `src/personas/infrastructure/ui/http/controllers/`
 * se registra bajo `/src/personas/<nombre_fichero_sin_php>` admitiendo GET y POST
 * para compatibilidad durante la migracion desde `apps/personas/...`.
 */

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/personas/stgr_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/stgr_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/personas/persona_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/persona_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/personas/persona_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/persona_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/personas/traslado_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/traslado_update.php';
    });
};
