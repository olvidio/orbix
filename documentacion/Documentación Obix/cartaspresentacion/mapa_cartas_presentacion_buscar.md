```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_cartaspresentacion_controller_cartas_presentacion_buscar_php(["cartas_presentacion_buscar.php"]):::controller --> apps_cartaspresentacion_view_cartas_presentacion_buscar_html_twig[["cartas_presentacion_buscar.html.twig"]]:::vista
    apps_cartaspresentacion_view_cartas_presentacion_buscar_html_twig --> apps_cartaspresentacion_controller_cartas_presentacion_lista_php(["cartas_presentacion_lista.php"]):::controller
```