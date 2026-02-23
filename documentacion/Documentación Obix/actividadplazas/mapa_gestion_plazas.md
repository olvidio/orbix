```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividadplazas_controller_gestion_plazas_php(["gestion_plazas.php"]):::controller --> apps_actividadplazas_view_gestion_plazas_phtml[["gestion_plazas.phtml"]]:::vista
    apps_actividadplazas_view_gestion_plazas_phtml --> apps_actividadplazas_controller_gestion_plazas_php(["gestion_plazas.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_actividadplazas_view_gestion_plazas_phtml: $oHash->getCamposHtml(); [DESTÍ NO RESOLT]
```