<?php
// Rutas del módulo Inventario
// Convención: este archivo retorna un callable que recibe (RouteCollector $r)
// para registrar las rutas del módulo. Así evitamos depender del ámbito.

return static function ($r) {
    // Convención: cada archivo en src/inventario/infrastructure/ui/http/controllers/*.php
    // se mapea a la ruta /inventario/<nombre_archivo_sin_php>
    // Permitimos GET y POST para máxima compatibilidad durante la migración.

    $r->addRoute(['GET','POST'], '/src/inventario/cabecera_pie_txt_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cabecera_pie_txt_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/cabecera_pie_txt', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/cabecera_pie_txt.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/doc_asignar_ctr_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/doc_asignar_ctr_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/doc_asignar_dlb_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/doc_asignar_dlb_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/documentos_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/documentos_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_add_doc', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_add_doc.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_del_doc', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_del_doc.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_doc_casa', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_doc_casa.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_egm', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_egm.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_eliminar_grupo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_eliminar_grupo.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_eliminar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_eliminar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_lista_activ_equipaje', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_lista_activ_equipaje.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_lista_activ_periodo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_lista_activ_periodo.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_lista_activ_sel', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_lista_activ_sel.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_movimientos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_movimientos.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_nuevo_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_nuevo_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_texto_listado_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_texto_listado_guardar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/equipajes_update_grupo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/equipajes_update_grupo.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/inventario_ctr', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/inventario_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/inventario_dlb', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/inventario_dlb.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/inventario_css_inline_data', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/inventario_css_inline_data.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_casas_posibles_periodo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_casas_posibles_periodo.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_colecciones', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_colecciones.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_de_ctr_con_docs', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_de_ctr_con_docs.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_de_ctr', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_de_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_asignados_por_tipo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_asignados_por_tipo.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_asignar_ctr', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_asignar_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_asignar_dlb', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_asignar_dlb.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_con_observaciones', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_con_observaciones.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_de_ctr', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_de_ctr.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_de_dlb', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_de_dlb.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_de_egm', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_de_egm.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_de_lugar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_de_lugar.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_en_busqueda', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_en_busqueda.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_libres', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_libres.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_no_asignados_por_tipo', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_no_asignados_por_tipo.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_docs_perdidos', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_docs_perdidos.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_equipajes_desde_fecha', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_equipajes_desde_fecha.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_equipajes_posibles_maletas', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_equipajes_posibles_maletas.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_lugares_de_ubi', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_lugares_de_ubi.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/lista_tipo_doc', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/lista_tipo_doc.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/texto_de_egm', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/texto_de_egm.php';
    });
    $r->addRoute(['GET','POST'], '/src/inventario/traslado_doc_guardar', function () {
        require __DIR__ . '/../infrastructure/ui/http/controllers/traslado_doc_guardar.php';
    });
};
