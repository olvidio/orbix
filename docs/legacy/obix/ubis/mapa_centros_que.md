```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_ubis_controller_centros_que_php(["centros_que.php"]):::controller --> apps_ubis_view_centros_que_html_twig[["centros_que.html.twig"]]:::vista
    apps_ubis_view_centros_que_html_twig --> apps_ubis_controller_centros_ajax_php(["centros_ajax.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_ubis_view_centros_que_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
```