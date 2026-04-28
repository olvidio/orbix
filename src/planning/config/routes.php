<?php

// Rutas del modulo `planning`. Cada endpoint vive en
// `src/planning/infrastructure/ui/http/controllers/` y responde JSON
// mediante `frontend\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/planning/planning_ctr_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/planning_ctr_select_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/planning/planning_persona_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/planning_persona_select_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/planning/planning_persona_ver_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/planning_persona_ver_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/planning/planning_zones_que_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/planning_zones_que_data.php';
    });
};
