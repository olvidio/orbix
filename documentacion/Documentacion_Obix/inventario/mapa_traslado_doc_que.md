```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_inventario_controller_traslado_doc_que_php(["traslado_doc_que.php"]):::controller --> frontend_inventario_view_traslado_doc_que_phtml[["traslado_doc_que.phtml"]]:::vista
    frontend_inventario_view_traslado_doc_que_phtml --> src_inventario_infrastructure_controllers_lista_lugares_de_ubi_php(["lista_lugares_de_ubi.php"]):::controller
    frontend_inventario_view_traslado_doc_que_phtml --> frontend_inventario_controller_traslado_doc_lista_php(["traslado_doc_lista.php"]):::controller
    frontend_inventario_view_traslado_doc_que_phtml --> src_inventario_infrastructure_controllers_traslado_doc_guardar_php(["traslado_doc_guardar.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_inventario_view_traslado_doc_que_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
```