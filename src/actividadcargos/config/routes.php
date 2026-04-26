<?php

// Rutas del modulo `actividadcargos`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/actividadcargos/infrastructure/ui/http/controllers/` y responde JSON via
// `frontend\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/actividadcargos/cargo_nuevo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cargo_nuevo.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadcargos/cargo_editar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cargo_editar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadcargos/cargo_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cargo_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadcargos/form_cargos_de_actividad_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadcargos/form_cargos_personas_en_actividad_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/form_cargos_personas_en_actividad_data.php';
    });
};
