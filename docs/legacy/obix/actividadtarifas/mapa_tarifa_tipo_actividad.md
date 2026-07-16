```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividadtarifas_controller_tarifa_tipo_actividad_php(["tarifa_tipo_actividad.php"]):::controller --> apps_actividadtarifas_view_tarifa_tipo_actividad_phtml[["tarifa_tipo_actividad.phtml"]]:::vista
```