```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_casas_controller_casas_resumen_php(["casas_resumen.php"]):::controller --> apps_casas_view_casa_resumen_que_html_twig[["casa_resumen_que.html.twig"]]:::vista
    apps_casas_view_casa_resumen_que_html_twig --> apps_casas_controller_casas_resumen_ajax_php(["casas_resumen_ajax.php"]):::controller
```