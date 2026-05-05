<?php
// Rutas del módulo Pasarela.
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Cada archivo en
// src/pasarela/infrastructure/ui/http/controllers/*.php se mapea a la ruta
// /src/pasarela/<nombre_archivo_sin_php>.

return static function ($r) {
    // Parámetro: fecha_activacion
    $r->addRoute(['GET', 'POST'], '/src/pasarela/activacion_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/activacion_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/activacion_excepcion_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/activacion_excepcion_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/activacion_excepcion_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/activacion_excepcion_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/activacion_default_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/activacion_default_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/activacion_default_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/activacion_default_data.php';
    });

    // Parámetro: contribucion_no_duerme
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_no_duerme_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_no_duerme_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_no_duerme_excepcion_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_no_duerme_excepcion_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_no_duerme_default_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_no_duerme_default_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_no_duerme_default_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_no_duerme_default_data.php';
    });

    // Parámetro: contribucion_reserva
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_reserva_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_reserva_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_reserva_excepcion_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_reserva_excepcion_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_reserva_excepcion_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_reserva_excepcion_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_reserva_default_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_reserva_default_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/contribucion_reserva_default_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/contribucion_reserva_default_data.php';
    });

    // Parámetro: nombre (sin valor por defecto)
    $r->addRoute(['GET', 'POST'], '/src/pasarela/nombre_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/nombre_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/nombre_excepcion_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/nombre_excepcion_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/nombre_excepcion_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/nombre_excepcion_eliminar.php';
    });

    // Datos auxiliares
    $r->addRoute(['GET', 'POST'], '/src/pasarela/tipo_activ_txt_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tipo_activ_txt_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/pasarela/exportar_actividades_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/exportar_actividades_data.php';
    });
};
