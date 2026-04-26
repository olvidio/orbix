<?php

/**
 * Rutas del modulo `cartaspresentacion`. Las registra `public/index.php`
 * via glob sobre `src/*\/config/routes.php`. Cada endpoint vive en
 * `src/cartaspresentacion/infrastructure/ui/http/controllers/` y responde
 * JSON estandar mediante `frontend\shared\web\ContestarJson::enviar(...)`.
 */
return static function ($r) {
    $base = __DIR__ . '/../infrastructure/ui/http/controllers';

    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/cartas_presentacion_shell_data', static function () use ($base) {
        require $base . '/cartas_presentacion_shell_data.php';
    });

    // Pantalla de busqueda: opciones de region/pais/delegacion.
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/cartas_presentacion_buscar_data', static function () use ($base) {
        require $base . '/cartas_presentacion_buscar_data.php';
    });

    // Pantalla de listado: modos `lista_dl`, `lista_todo`, `get` (filtros).
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/cartas_presentacion_lista_data', static function () use ($base) {
        require $base . '/cartas_presentacion_lista_data.php';
    });

    // Pantalla principal (modificar): opciones del desplegable de poblaciones.
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/poblaciones_data', static function () use ($base) {
        require $base . '/poblaciones_data.php';
    });

    // Pantalla principal (modificar): listado de centros con estado de carta.
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/ubis_lista_data', static function () use ($base) {
        require $base . '/ubis_lista_data.php';
    });

    // Formulario de modificacion de una CartaPresentacion.
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/carta_presentacion_form_data', static function () use ($base) {
        require $base . '/carta_presentacion_form_data.php';
    });

    // Mutaciones.
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/carta_presentacion_update', static function () use ($base) {
        require $base . '/carta_presentacion_update.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cartaspresentacion/carta_presentacion_eliminar', static function () use ($base) {
        require $base . '/carta_presentacion_eliminar.php';
    });
};
