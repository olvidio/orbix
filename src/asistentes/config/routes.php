<?php

// Rutas del modulo `asistentes`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/asistentes/infrastructure/ui/http/controllers/` y responde JSON via
// `frontend\shared\web\ContestarJson::enviar(...)`.
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

    $base = __DIR__ . '/../infrastructure/ui/http/controllers';
    $r->addRoute(['GET', 'POST'], '/src/asistentes/lista_ultim_que_ctr_data', function () use ($base) {
        require $base . '/lista_ultim_que_ctr_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/que_ctr_lista_data', function () use ($base) {
        require $base . '/que_ctr_lista_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/lista_est_ctr_data', function () use ($base) {
        require $base . '/lista_est_ctr_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/lista_activ_ctr_data', function () use ($base) {
        require $base . '/lista_activ_ctr_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/lista_asis_conjunto_activ_data', function () use ($base) {
        require $base . '/lista_asis_conjunto_activ_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/lista_ultima_activ_data', function () use ($base) {
        require $base . '/lista_ultima_activ_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/activ_pendientes_select_data', function () use ($base) {
        require $base . '/activ_pendientes_select_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/lista_asistentes_data', function () use ($base) {
        require $base . '/lista_asistentes_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/form_asistentes_a_una_actividad_data', function () use ($base) {
        require $base . '/form_asistentes_a_una_actividad_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/form_actividades_de_una_persona_data', function () use ($base) {
        require $base . '/form_actividades_de_una_persona_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/tabla_peticiones_data', function () use ($base) {
        require $base . '/tabla_peticiones_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asistentes/asistente_mover_data', function () use ($base) {
        require $base . '/asistente_mover_data.php';
    });
};
