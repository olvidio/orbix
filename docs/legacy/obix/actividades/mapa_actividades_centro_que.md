```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividades_controller_actividades_centro_que_php(["actividades_centro_que.php"]):::controller --> apps_actividades_view_actividades_centro_que_phtml[["actividades_centro_que.phtml"]]:::vista
    apps_actividades_view_actividades_centro_que_phtml --> programas_centro_ajax_php(["centro_ajax.php"]):::controller
    class programas_centro_ajax_php error;
```