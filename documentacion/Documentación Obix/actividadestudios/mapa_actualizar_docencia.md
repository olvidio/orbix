```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividadestudios_controller_actualizar_docencia_php(["actualizar_docencia.php"]):::controller --> apps_actividadestudios_view_actualizar_docencia_phtml[["actualizar_docencia.phtml"]]:::vista
    apps_actividadestudios_view_actualizar_docencia_phtml --> apps_actividadestudios_controller_actualizar_docencia_php(["actualizar_docencia.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_actividadestudios_view_actualizar_docencia_phtml: $oHashPeriodo->getCamposHtml(); [DESTÍ NO RESOLT]
```