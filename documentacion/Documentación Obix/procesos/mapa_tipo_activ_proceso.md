```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_procesos_controller_tipo_activ_proceso_php(["tipo_activ_proceso.php"]):::controller --> apps_procesos_view_tipo_activ_proceso_html_twig[["tipo_activ_proceso.html.twig"]]:::vista
    apps_procesos_view_tipo_activ_proceso_html_twig --> apps_procesos_controller_tipo_activ_proceso_ajax_php(["tipo_activ_proceso_ajax.php"]):::controller
```