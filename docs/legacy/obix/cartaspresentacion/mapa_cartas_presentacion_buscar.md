```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef endpoint fill:#9f9,stroke:#333,stroke-width:2px;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_cartaspresentacion_controller_cartas_presentacion_buscar_php(["cartas_presentacion_buscar.php"]):::controller --> src_cartaspresentacion_cartas_presentacion_buscar_data{{"/src/cartaspresentacion/cartas_presentacion_buscar_data"}}:::endpoint
    frontend_cartaspresentacion_controller_cartas_presentacion_buscar_php --> frontend_cartaspresentacion_view_cartas_presentacion_buscar_phtml[["cartas_presentacion_buscar.phtml"]]:::vista
    frontend_cartaspresentacion_view_cartas_presentacion_buscar_phtml --> frontend_cartaspresentacion_controller_cartas_presentacion_lista_php(["cartas_presentacion_lista.php"]):::controller
    frontend_cartaspresentacion_controller_cartas_presentacion_lista_php --> src_cartaspresentacion_cartas_presentacion_lista_data{{"/src/cartaspresentacion/cartas_presentacion_lista_data"}}:::endpoint
```
