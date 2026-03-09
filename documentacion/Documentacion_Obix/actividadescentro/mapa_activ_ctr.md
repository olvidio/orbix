```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividadescentro_controller_activ_ctr_php(["activ_ctr.php"]):::controller --> apps_actividadescentro_view_activ_ctr_html_twig[["activ_ctr.html.twig"]]:::vista
    apps_actividadescentro_view_activ_ctr_html_twig --> apps_actividadescentro_controller_activ_ctr_ajax_php(["activ_ctr_ajax.php"]):::controller
```