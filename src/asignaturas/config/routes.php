<?php

return static function ($r) {
    $r->addRoute(['GET', 'POST'], '/src/asignaturas/asignaturas_map_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asignaturas_map_data.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/asignaturas/asignaturas_con_separador_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/asignaturas_con_separador_data.php';
    });
};
