<?php
// Rutas del módulo Inventario
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/inventario/infrastructure/controllers/*.php
    // se mapea a la ruta /inventario/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/inventario/cabecera_pie_txt_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/cabecera_pie_txt_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/cabecera_pie_txt', function () {
        require __DIR__ . '/../infrastructure/controllers/cabecera_pie_txt.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/doc_asignar_ctr_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/doc_asignar_ctr_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/doc_asignar_dlb_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/doc_asignar_dlb_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/documentos_form', function () {
        require __DIR__ . '/../infrastructure/controllers/documentos_form.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/documentos_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/documentos_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_add_doc', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_add_doc.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_del_doc', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_del_doc.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_doc_casa', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_doc_casa.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_egm', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_egm.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_eliminar_grupo', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_eliminar_grupo.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_eliminar', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_lista_activ_equipaje', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_lista_activ_equipaje.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_lista_activ_periodo', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_lista_activ_periodo.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_lista_activ_sel', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_lista_activ_sel.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_movimientos', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_movimientos.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_nuevo_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_nuevo_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_texto_listado_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_texto_listado_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/equipajes_update_grupo', function () {
        require __DIR__ . '/../infrastructure/controllers/equipajes_update_grupo.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/inventario_ctr', function () {
        require __DIR__ . '/../infrastructure/controllers/inventario_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/inventario_dlb', function () {
        require __DIR__ . '/../infrastructure/controllers/inventario_dlb.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_casas_posibles_periodo', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_casas_posibles_periodo.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_colecciones', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_colecciones.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_de_ctr_con_docs', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_de_ctr_con_docs.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_de_ctr', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_de_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_asignados_por_tipo', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_asignados_por_tipo.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_asignar_ctr', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_asignar_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_asignar_dlb', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_asignar_dlb.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_con_observaciones', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_con_observaciones.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_de_ctr', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_de_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_de_dlb', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_de_dlb.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_de_egm', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_de_egm.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_de_lugar', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_de_lugar.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_en_busqueda', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_en_busqueda.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_libres', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_libres.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_no_asignados_por_tipo', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_no_asignados_por_tipo.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_docs_perdidos', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_docs_perdidos.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_equipajes_desde_fecha', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_equipajes_desde_fecha.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_equipajes_posibles_maletas', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_equipajes_posibles_maletas.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_lugares_de_ubi', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_lugares_de_ubi.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/lista_tipo_doc', function () {
        require __DIR__ . '/../infrastructure/controllers/lista_tipo_doc.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/texto_de_egm', function () {
        require __DIR__ . '/../infrastructure/controllers/texto_de_egm.php';
    });
    $r->addRoute(['GET','POST'], '/inventario/traslado_doc_guardar', function () {
        require __DIR__ . '/../infrastructure/controllers/traslado_doc_guardar.php';
    });
};
