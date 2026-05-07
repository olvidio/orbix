<?php

return static function ($r) {
    // --- Mutaciones (AJAX) ---
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_syncro', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_syncro.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_crear', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_crear.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_unir', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_unir.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_desunir', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_desunir.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_trasladar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_trasladar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_trasladar_a', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_trasladar_a.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_baja', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_baja.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_crear_todos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_crear_todos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/refrescar_bdu', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/refrescar_bdu.php';
    });

    // --- Datos para vistas (lectura) ---
    $r->addRoute(['GET', 'POST'], '/src/dbextern/sincro_index_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/sincro_index_datos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/ver_listas_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_listas_datos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/ver_orbix_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_orbix_datos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/ver_traslados_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_traslados_datos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/ver_desaparecidos_de_listas_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_desaparecidos_de_listas_datos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/ver_desaparecidos_de_orbix_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_desaparecidos_de_orbix_datos.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/dbextern/ver_orbix_otradl_datos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ver_orbix_otradl_datos.php';
    });
};
