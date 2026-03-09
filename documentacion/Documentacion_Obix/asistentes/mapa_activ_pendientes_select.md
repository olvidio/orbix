```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_asistentes_controller_activ_pendientes_select_php(["activ_pendientes_select.php"]):::controller --> apps_asistentes_view_activ_pendientes_phtml[["activ_pendientes.phtml"]]:::vista
```