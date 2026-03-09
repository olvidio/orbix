```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_inventario_controller_equipajes_ver_php(["equipajes_ver.php"]):::controller --> frontend_inventario_view_equipajes_ver_phtml[["equipajes_ver.phtml"]]:::vista
    frontend_inventario_view_equipajes_ver_phtml --> src_inventario_infrastructure_controllers_equipajes_texto_listado_guardar_php(["equipajes_texto_listado_guardar.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_form_texto_listado_php(["equipajes_form_texto_listado.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> src_inventario_infrastructure_controllers_equipajes_eliminar_php(["equipajes_eliminar.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> src_inventario_infrastructure_controllers_equipajes_del_doc_php(["equipajes_del_doc.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_form_del_php(["equipajes_form_del.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> src_inventario_infrastructure_controllers_equipajes_add_doc_php(["equipajes_add_doc.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_docs_libres_php(["equipajes_docs_libres.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_form_add_php(["equipajes_form_add.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> src_inventario_infrastructure_controllers_equipajes_eliminar_grupo_php(["equipajes_eliminar_grupo.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> src_inventario_infrastructure_controllers_equipajes_update_grupo_php(["equipajes_update_grupo.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_lista_docs_php(["equipajes_lista_docs.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_ver_docs_php(["equipajes_ver_docs.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_posibles_maletas_php(["equipajes_posibles_maletas.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_desplegable_php(["equipajes_desplegable.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_doc_casa_php(["equipajes_doc_casa.php"]):::controller
    frontend_inventario_view_equipajes_ver_phtml --> frontend_inventario_controller_equipajes_imprimir_php(["equipajes_imprimir.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_inventario_view_equipajes_ver_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
    frontend_inventario_controller_equipajes_form_texto_listado_php(["equipajes_form_texto_listado.php"]):::controller --> frontend_inventario_view_equipajes_form_texto_listado_phtml[["equipajes_form_texto_listado.phtml"]]:::vista
    frontend_inventario_controller_equipajes_form_del_php(["equipajes_form_del.php"]):::controller --> frontend_inventario_view_equipajes_form_del_phtml[["equipajes_form_del.phtml"]]:::vista
    frontend_inventario_controller_equipajes_form_add_php(["equipajes_form_add.php"]):::controller --> frontend_inventario_view_equipajes_form_add_phtml[["equipajes_form_add.phtml"]]:::vista
    frontend_inventario_controller_equipajes_lista_docs_php(["equipajes_lista_docs.php"]):::controller --> frontend_inventario_view_equipajes_doc_maleta_phtml[["equipajes_doc_maleta.phtml"]]:::vista
    frontend_inventario_controller_equipajes_doc_casa_php(["equipajes_doc_casa.php"]):::controller --> frontend_inventario_view_equipajes_doc_casa_phtml[["equipajes_doc_casa.phtml"]]:::vista
```