<?php

// Rutas del modulo `asistentes`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/asistentes/infrastructure/ui/http/controllers/` y responde JSON via
// `web\ContestarJson::enviar(...)`.
return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/asistentes/asistente_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asistente_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/asistentes/asistente_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asistente_guardar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/asistentes/asistente_plaza_asignar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asistente_plaza_asignar.php';
    });
};
