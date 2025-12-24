<?php
// Rutas del módulo Shared
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/shared/infrastructure/controllers/*.php
    // se mapea a la ruta /shared/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/shared/locales_posibles', function () {
        require __DIR__ . '/../infrastructure/controllers/locales_posibles.php';
    });
    $r->addRoute(['GET','POST'], '/shared/tablaDB_buscar_datos', function () {
        require __DIR__ . '/../infrastructure/controllers/tablaDB_buscar_datos.php';
    });
    $r->addRoute(['GET','POST'], '/shared/tablaDB_depende_datos', function () {
        require __DIR__ . '/../infrastructure/controllers/tablaDB_depende_datos.php';
    });
    $r->addRoute(['GET','POST'], '/shared/tablaDB_formulario_datos', function () {
        require __DIR__ . '/../infrastructure/controllers/tablaDB_formulario_datos.php';
    });
    $r->addRoute(['GET','POST'], '/shared/tablaDB_lista_datos', function () {
        require __DIR__ . '/../infrastructure/controllers/tablaDB_lista_datos.php';
    });
    $r->addRoute(['GET','POST'], '/shared/tablaDB_update', function () {
        require __DIR__ . '/../infrastructure/controllers/tablaDB_update.php';
    });
};
