<?php

// Rutas del modulo `dossiers`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/dossiers/infrastructure/ui/http/controllers/` y responde JSON via
// `src\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/dossiers/tipo_dossier_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_dossier_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/dossiers/tipo_dossier_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_dossier_guardar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/dossiers/perm_dossiers_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_dossiers_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dossiers/perm_dossier_ver_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/perm_dossier_ver_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dossiers/dossiers_lista_fichas_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/dossiers_lista_fichas_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dossiers/dossiers_ver_pantalla_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php';
    });
};
