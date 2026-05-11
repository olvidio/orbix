<?php

// Rutas del modulo `notas`. Las registra `public/index.php` via glob sobre
// `src/*/config/routes.php`. Cada endpoint vive en
// `src/notas/infrastructure/ui/http/controllers/` y responde JSON mediante
// `src\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    // Slice 1: Mutaciones criticas (actas, notas de persona, pdf, tessera).
    $r->addRoute(['GET', 'POST'], '/src/notas/acta_nueva', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_nueva.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_modificar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_modificar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/persona_nota_nueva', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/persona_nota_nueva.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/persona_nota_editar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/persona_nota_editar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/persona_nota_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/persona_nota_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_pdf_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_pdf_eliminar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_pdf_subir', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_pdf_subir.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/tessera_copiar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tessera_copiar.php';
    });

    // Slice 3: autocomplete acta (examinadores, asignaturas).
    $r->addRoute(['GET', 'POST'], '/src/notas/examinadores_search', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/examinadores_search.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/asignaturas_search', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asignaturas_search.php';
    });

    // Slice 3: Lectura de actas.
    $r->addRoute(['GET', 'POST'], '/src/notas/acta_listado_anual_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_listado_anual_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_pdf_download', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_pdf_download.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_imprimir_presentacion_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_imprimir_presentacion_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/asig_faltan_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asig_faltan_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/asig_faltan_personas_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asig_faltan_personas_select_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/acta_ver_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/acta_ver_form_data.php';
    });

    // Slice 5: Seleccion destino para copiar tessera.
    $r->addRoute(['GET', 'POST'], '/src/notas/tessera_copiar_select_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tessera_copiar_select_data.php';
    });

    // Slice 9 (post limpieza): notas_ajax dispatcher split en endpoints dedicados.
    $r->addRoute(['GET', 'POST'], '/src/notas/buscar_acta', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/buscar_acta.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/posibles_opcionales_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/posibles_opcionales_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/posibles_preceptores_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/posibles_preceptores_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/actividades_buscar_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/actividades_buscar_data.php';
    });

    // Slice 17: form_notas_de_una_persona deja de importar NotaPersonaFormData y lo consume via PostRequest.
    $r->addRoute(['GET', 'POST'], '/src/notas/nota_persona_form_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/nota_persona_form_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/asignaturas_pendientes_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asignaturas_pendientes_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/asignaturas_pendientes_resumen_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asignaturas_pendientes_resumen_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/informe_stgr_agd_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/informe_stgr_agd_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/informe_stgr_n_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/informe_stgr_n_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/informe_stgr_profesores_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/informe_stgr_profesores_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/tessera_imprimir_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tessera_imprimir_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/tessera_ver_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/tessera_ver_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/comprobar_notas_constants_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/comprobar_notas_constants_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/notas/comprobar_notas_page_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/comprobar_notas_page_data.php';
    });
};
