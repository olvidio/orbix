<?php

/**
 * Rutas del modulo `cambios`. Las registra `public/index.php` via glob sobre
 * `src/*\/config/routes.php`. Cada endpoint vive en
 * `src/cambios/infrastructure/ui/http/controllers/` y responde JSON estandar
 * mediante `src\shared\web\ContestarJson::enviar(...)`.
 */
return static function ($r) {
    $base = __DIR__ . '/../infrastructure/ui/http/controllers';

    // Listado de `CambioUsuarioObjetoPref` de un usuario (tabla del form
    // de avisos por usuario).
    $r->addRoute(['GET', 'POST'], '/src/cambios/usuario_form_avisos_data', static function () use ($base) {
        require $base . '/usuario_form_avisos_data.php';
    });

    // Listado de avisos `CambioUsuario` (pantalla `avisos_generar`).
    $r->addRoute(['GET', 'POST'], '/src/cambios/avisos_generar_lista_data', static function () use ($base) {
        require $base . '/avisos_generar_lista_data.php';
    });

    // Mutaciones: borrado de avisos.
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_eliminar', static function () use ($base) {
        require $base . '/cambio_usuario_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_eliminar_hasta_fecha', static function () use ($base) {
        require $base . '/cambio_usuario_eliminar_hasta_fecha.php';
    });

    // Pantalla `usuario_avisos_pref`: datos del formulario principal.
    $r->addRoute(['GET', 'POST'], '/src/cambios/usuario_avisos_pref_form_data', static function () use ($base) {
        require $base . '/usuario_avisos_pref_form_data.php';
    });

    // Pantalla `usuario_avisos_pref`: fragmentos y mutaciones.
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_propiedad_pref_item_data', static function () use ($base) {
        require $base . '/cambio_usuario_propiedad_pref_item_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_objeto_pref_propiedades_data', static function () use ($base) {
        require $base . '/cambio_usuario_objeto_pref_propiedades_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_objeto_pref_fases_data', static function () use ($base) {
        require $base . '/cambio_usuario_objeto_pref_fases_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_propiedad_pref_preview', static function () use ($base) {
        require $base . '/cambio_usuario_propiedad_pref_preview.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_objeto_pref_guardar', static function () use ($base) {
        require $base . '/cambio_usuario_objeto_pref_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_objeto_pref_eliminar', static function () use ($base) {
        require $base . '/cambio_usuario_objeto_pref_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/cambios/cambio_usuario_propiedad_pref_guardar_todas', static function () use ($base) {
        require $base . '/cambio_usuario_propiedad_pref_guardar_todas.php';
    });
};
