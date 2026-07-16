```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_encargossacd_controller_sacd_ficha_php(["sacd_ficha.php"]):::controller --> apps_encargossacd_view_sacd_ficha_html_twig[["sacd_ficha.html.twig"]]:::vista
    apps_encargossacd_view_sacd_ficha_html_twig --> apps_encargossacd_controller_sacd_ficha_ajax_php(["sacd_ficha_ajax.php"]):::controller
```