<?php

// Rutas del modulo `actividadescentro`. Las registra `public/index.php` via
// glob sobre `src/*/config/routes.php`. Cada endpoint vive en
// `src/actividadescentro/infrastructure/ui/http/controllers/` y responde
// JSON mediante `src\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    // Mutaciones.
    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/centro_encargado_asignar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centro_encargado_asignar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/centro_encargado_reordenar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centro_encargado_reordenar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/centro_encargado_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centro_encargado_eliminar.php';
    });

    // Lecturas (data builders).
    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/centros_encargados_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_encargados_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/centros_disponibles_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/centros_disponibles_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/lista_actividades_ctr_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_actividades_ctr_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadescentro/activ_ctr_shell_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/activ_ctr_shell_data.php';
    });
};
