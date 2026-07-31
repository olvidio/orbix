<?php

// Rutas HTTP JSON del módulo `encargossacd`. Registrar aquí los endpoints bajo
// `/src/encargossacd/...` cuando se extraigan casos de uso desde controladores
// legacy (patrón `refactor.md`). Las registra `public/index.php` vía glob sobre
// `src/*/config/routes.php`.
return static function ($r) {
    $base = __DIR__ . '/../infrastructure/ui/http/controllers';

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_lst_tipo_enc_data', function () use ($base) {
        require $base . '/encargo_lst_tipo_enc_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_ver_nuevo', function () use ($base) {
        require $base . '/encargo_ver_nuevo.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_ver_editar', function () use ($base) {
        require $base . '/encargo_ver_editar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_ver_eliminar', function () use ($base) {
        require $base . '/encargo_ver_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/zonas_get_select_data', function () use ($base) {
        require $base . '/zonas_get_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/ctr_get_select_data', function () use ($base) {
        require $base . '/ctr_get_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/comprobaciones_ctr', function () use ($base) {
        require $base . '/comprobaciones_ctr.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_com_txt_get', function () use ($base) {
        require $base . '/listas_com_txt_get.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_com_txt_update', function () use ($base) {
        require $base . '/listas_com_txt_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/horario_ver_data', function () use ($base) {
        require $base . '/horario_ver_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/horario_update_data', function () use ($base) {
        require $base . '/horario_update_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/horario_sacd_ver_data', function () use ($base) {
        require $base . '/horario_sacd_ver_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/horario_sacd_update_data', function () use ($base) {
        require $base . '/horario_sacd_update_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/opciones_seccion_data', function () use ($base) {
        require $base . '/opciones_seccion_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/ctr_ficha_data', function () use ($base) {
        require $base . '/ctr_ficha_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/ctr_get_ficha_data', function () use ($base) {
        require $base . '/ctr_get_ficha_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/ctr_ficha_update', function () use ($base) {
        require $base . '/ctr_ficha_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_select_data', function () use ($base) {
        require $base . '/encargo_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_ver_data', function () use ($base) {
        require $base . '/encargo_ver_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/encargo_horario_select_data', function () use ($base) {
        require $base . '/encargo_horario_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/sacd_select_data', function () use ($base) {
        require $base . '/sacd_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/sacd_ficha_data', function () use ($base) {
        require $base . '/sacd_ficha_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/sacd_ficha_update', function () use ($base) {
        require $base . '/sacd_ficha_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/sacd_ausencias_get_data', function () use ($base) {
        require $base . '/sacd_ausencias_get_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/sacd_ausencias_update', function () use ($base) {
        require $base . '/sacd_ausencias_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/sacd_ausencias_jefe_zona_data', function () use ($base) {
        require $base . '/sacd_ausencias_jefe_zona_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_com_txt_data', function () use ($base) {
        require $base . '/listas_com_txt_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_a_data', function () use ($base) {
        require $base . '/listas_a_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_b_data', function () use ($base) {
        require $base . '/listas_b_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_c_data', function () use ($base) {
        require $base . '/listas_c_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_d_data', function () use ($base) {
        require $base . '/listas_d_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_exigencia_ctr_data', function () use ($base) {
        require $base . '/listas_exigencia_ctr_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_com_ctr_data', function () use ($base) {
        require $base . '/listas_com_ctr_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_com_sacd_data', function () use ($base) {
        require $base . '/listas_com_sacd_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/listas_cl_data', function () use ($base) {
        require $base . '/listas_cl_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/propuestas_ajax', function () use ($base) {
        require $base . '/propuestas_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/propuestas_aprobar', function () use ($base) {
        require $base . '/propuestas_aprobar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/propuestas_lista_enc_data', function () use ($base) {
        require $base . '/propuestas_lista_enc_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/encargossacd/propuestas_lista_sacd_data', function () use ($base) {
        require $base . '/propuestas_lista_sacd_data.php';
    });
};
