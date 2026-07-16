```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_menus_controller_menus_exportar_form_php(["menus_exportar_form.php"]):::controller --> frontend_menus_view_menus_exportar_form_phtml[["menus_exportar_form.phtml"]]:::vista
    frontend_menus_view_menus_exportar_form_phtml --> src_menus_infrastructure_controllers_menus_exportar_php(["menus_exportar.php"]):::controller
```