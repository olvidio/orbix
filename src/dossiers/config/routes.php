<?php

// Rutas del modulo `dossiers`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/dossiers/infrastructure/ui/http/controllers/` y responde JSON via
// `web\ContestarJson::enviar(...)`.
return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/dossiers/tipo_dossier_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_dossier_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/dossiers/tipo_dossier_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_dossier_guardar.php';
    });
};
