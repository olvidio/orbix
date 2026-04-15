<?php
// Rutas del módulo Ubis
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/ubis/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /ubis/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_tabla', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_tabla.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_editar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_editar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_desc_lista', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_desc_lista.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/teleco_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/teleco_eliminar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_que', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_que.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_tabla', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_tabla.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_editar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_editar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_asignar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_asignar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/direcciones_quitar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/direcciones_quitar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_guardar.php';
    });
    $r->addRoute(['GET', 'POST'], '/src/ubis/ubis_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/ubis_eliminar.php';
    });

};
