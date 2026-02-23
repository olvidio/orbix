```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_ubis_controller_calendario_periodos_php(["calendario_periodos.php"]):::controller --> apps_ubis_view_calendario_periodos_phtml[["calendario_periodos.phtml"]]:::vista
    apps_ubis_view_calendario_periodos_phtml --> apps_ubis_controller_calendario_periodos_ajax_php(["calendario_periodos_ajax.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_ubis_view_calendario_periodos_phtml: $oForm->getDesplCasas()->desplegable(); [DESTÍ NO RESOLT]
```