```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_inventario_controller_cabecera_pie_txt_php(["cabecera_pie_txt.php"]):::controller --> frontend_inventario_view_cabecera_pie_txt_phtml[["cabecera_pie_txt.phtml"]]:::vista
    frontend_inventario_view_cabecera_pie_txt_phtml --> src_inventario_infrastructure_controllers_cabecera_pie_txt_guardar_php(["cabecera_pie_txt_guardar.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_inventario_view_cabecera_pie_txt_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
```