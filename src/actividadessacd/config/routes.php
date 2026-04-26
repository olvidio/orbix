<?php

/**
 * Rutas del modulo `actividadessacd`. Las registra `public/index.php` via
 * glob sobre `src/*\/config/routes.php`. Cada endpoint vive en
 * `src/actividadessacd/infrastructure/ui/http/controllers/` y responde
 * JSON mediante `frontend\shared\web\ContestarJson::enviar(...)`.
 */
return static function ($r) {
    // Mutaciones.
    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/sacd_asignar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sacd_asignar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/sacd_reordenar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sacd_reordenar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/sacd_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sacd_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/sacd_asignar_auto', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sacd_asignar_auto.php';
    });

    // Lecturas (data builders).
    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/sacds_encargados_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sacds_encargados_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/sacds_disponibles_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sacds_disponibles_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/lista_actividades_sacd_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_actividades_sacd_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/solapes_sacd_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/solapes_sacd_data.php';
    });

    // Textos de comunicacion a los sacd (slice `com_sacd_txt`).
    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/texto_comunicacion_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/texto_comunicacion_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/texto_comunicacion_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/texto_comunicacion_guardar.php';
    });

    // Comunicacion activ sacd (slice `com_sacd_activ_periodo` + `com_sacd_activ`).
    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/comunicacion_activ_sacd_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadessacd/comunicacion_activ_sacd_enviar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php';
    });
};
