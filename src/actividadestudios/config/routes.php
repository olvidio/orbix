<?php

// Rutas del modulo `actividadestudios`. Las registra `public/index.php` via
// glob sobre `src/*/config/routes.php`. Cada endpoint vive en
// `src/actividadestudios/infrastructure/ui/http/controllers/` y responde JSON
// estandar mediante `frontend\shared\web\ContestarJson::enviar(...)`.
return static function ($r) {
    $base = __DIR__ . '/../infrastructure/ui/http/controllers';

    // ----- Matriculas (dossiers 1303 / 3103) -------------------------------
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matricula_nueva', function () use ($base) {
        require $base . '/matricula_nueva.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matricula_editar', function () use ($base) {
        require $base . '/matricula_editar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matricula_eliminar', function () use ($base) {
        require $base . '/matricula_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/asistente_observ_est', function () use ($base) {
        require $base . '/asistente_observ_est.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/asistente_observ', function () use ($base) {
        require $base . '/asistente_observ.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/asistente_plan_est_ok', function () use ($base) {
        require $base . '/asistente_plan_est_ok.php';
    });

    // ----- Asignaturas de actividad (dossier 3005) -------------------------
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/actividad_asignatura_nueva', function () use ($base) {
        require $base . '/actividad_asignatura_nueva.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/actividad_asignatura_editar', function () use ($base) {
        require $base . '/actividad_asignatura_editar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/actividad_asignatura_eliminar', function () use ($base) {
        require $base . '/actividad_asignatura_eliminar.php';
    });

    // ----- Helpers UI (desplegables AJAX) ----------------------------------
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/profesores_desplegable_data', function () use ($base) {
        require $base . '/profesores_desplegable_data.php';
    });

    // ----- Matricula automatica (matricular) -------------------------------
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matricula_automatica', function () use ($base) {
        require $base . '/matricula_automatica.php';
    });

    // ----- Acta de notas (guardar borrador / grabar definitivas) -----------
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/acta_notas_matricula_guardar', function () use ($base) {
        require $base . '/acta_notas_matricula_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/acta_notas_definitivas_grabar', function () use ($base) {
        require $base . '/acta_notas_definitivas_grabar.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matriculas_lista_data', function () use ($base) {
        require $base . '/matriculas_lista_data.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matriculas_pendientes_data', function () use ($base) {
        require $base . '/matriculas_pendientes_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/ca_posibles_que_data', function () use ($base) {
        require $base . '/ca_posibles_que_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/plan_estudios_ca_data', function () use ($base) {
        require $base . '/plan_estudios_ca_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/matriculas_lista_otras_r_data', function () use ($base) {
        require $base . '/matriculas_lista_otras_r_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/ca_posibles_data', function () use ($base) {
        require $base . '/ca_posibles_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/e43_imprimir_mpdf_data', function () use ($base) {
        require $base . '/e43_imprimir_mpdf_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/acta_notas_data', function () use ($base) {
        require $base . '/acta_notas_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/form_matriculas_de_una_persona_data', function () use ($base) {
        require $base . '/form_matriculas_de_una_persona_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/e43_data', function () use ($base) {
        require $base . '/e43_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/form_asignaturas_de_una_actividad_data', function () use ($base) {
        require $base . '/form_asignaturas_de_una_actividad_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/lista_clases_ca_data', function () use ($base) {
        require $base . '/lista_clases_ca_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/actividadestudios/posibles_asignaturas_ca_data', function () use ($base) {
        require $base . '/posibles_asignaturas_ca_data.php';
    });
};
