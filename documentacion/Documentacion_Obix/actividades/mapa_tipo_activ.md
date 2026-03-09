```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividades_controller_tipo_activ_php(["tipo_activ.php"]):::controller --> apps_actividades_view_tipo_activ_html_twig[["tipo_activ.html.twig"]]:::vista
    apps_actividades_view_tipo_activ_html_twig --> apps_actividades_controller_tipo_activ_ajax_php(["tipo_activ_ajax.php"]):::controller
```