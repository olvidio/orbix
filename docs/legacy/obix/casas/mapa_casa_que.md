```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_casas_controller_casa_que_php(["casa_que.php"]):::controller --> apps_casas_view_casa_que_html_twig[["casa_que.html.twig"]]:::vista
    apps_casas_view_casa_que_html_twig --> apps_casas_controller_casa_ajax_php(["casa_ajax.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_casas_view_casa_que_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
```