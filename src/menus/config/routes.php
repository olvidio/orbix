<?php
// Rutas del módulo Menus
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/menus/infrastructure/controllers/*.php
    // se mapea a la ruta /menus/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/menus/grupmenu_coleccion', function () {
        require __DIR__ . '/../infrastructure/controllers/grupmenu_coleccion.php';
    });
    $r->addRoute(['GET','POST'], '/menus/grupmenu_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/grupmenu_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/menus/grupmenu_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/grupmenu_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/menus/grupmenu_info', function () {
        require __DIR__ . '/../infrastructure/controllers/grupmenu_info.php';
    });
    $r->addRoute(['GET','POST'], '/menus/grupmenu_lista', function () {
        require __DIR__ . '/../infrastructure/controllers/grupmenu_lista.php';
    });
    $r->addRoute(['GET','POST'], '/menus/lista_meta_menus', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_meta_menus.php';
    });
    $r->addRoute(['GET','POST'], '/menus/lista_templates', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_templates.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menu_copiar', function () {
        require __DIR__ . '/../infrastructure/controllers/menu_copiar.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menu_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/menu_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menu_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/menu_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menu_mover', function () {
        require __DIR__ . '/../infrastructure/controllers/menu_mover.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menus_exportar', function () {
        require __DIR__ . '/../infrastructure/controllers/menus_exportar.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menus_exportar_ref_a_ficheros', function () {
        require __DIR__ . '/../infrastructure/controllers/menus_exportar_ref_a_ficheros.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menus_generar_txt', function () {
        require __DIR__ . '/../infrastructure/controllers/menus_generar_txt.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menus_importar_de_ficheros_a_ref', function () {
        require __DIR__ . '/../infrastructure/controllers/menus_importar_de_ficheros_a_ref.php';
    });
    $r->addRoute(['GET','POST'], '/menus/menus_importar', function () {
        require __DIR__ . '/../infrastructure/controllers/menus_importar.php';
    });
};
