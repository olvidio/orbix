<?php

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/profesores/congresos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/congresos.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/profesores/docencia', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/docencia.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/profesores/profesor_asignatura_que', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/profesor_asignatura_que.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/profesores/profesor_asignatura_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/profesor_asignatura_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/profesores/ficha_profesor_stgr', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ficha_profesor_stgr.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/profesores/lista_por_departamentos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_por_departamentos.php';
    });
};
