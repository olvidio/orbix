<?php

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_sacd', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_sacd_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_sacd_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd_lista.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_sacd_lista_tot', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd_lista_tot.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_sacd_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_sacd_update.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_ctr', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_ctr.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_ctr_ajax', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_ctr_ajax.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_ctr_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_ctr_lista.php';
    });

    $r->addRoute(['GET', 'POST'], '/src/zonassacd/zona_ctr_update', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/zona_ctr_update.php';
    });
};
