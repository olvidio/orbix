```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef endpoint fill:#9f9,stroke:#333,stroke-width:2px;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_cartaspresentacion_controller_cartas_presentacion_php(["cartas_presentacion.php"]):::controller --> frontend_cartaspresentacion_view_cartas_presentacion_phtml[["cartas_presentacion.phtml"]]:::vista

    frontend_cartaspresentacion_view_cartas_presentacion_phtml --> frontend_ubis_controller_home_ubis_php(["home_ubis.php"]):::controller
    frontend_cartaspresentacion_view_cartas_presentacion_phtml --> frontend_cartaspresentacion_controller_cartas_presentacion_ubis_lista_php(["cartas_presentacion_ubis_lista.php"]):::controller
    frontend_cartaspresentacion_view_cartas_presentacion_phtml --> frontend_cartaspresentacion_controller_cartas_presentacion_form_php(["cartas_presentacion_form.php"]):::controller
    frontend_cartaspresentacion_view_cartas_presentacion_phtml --> src_cartaspresentacion_poblaciones_data{{"/src/cartaspresentacion/poblaciones_data"}}:::endpoint
    frontend_cartaspresentacion_view_cartas_presentacion_phtml --> src_cartaspresentacion_carta_presentacion_update{{"/src/cartaspresentacion/carta_presentacion_update"}}:::endpoint
    frontend_cartaspresentacion_view_cartas_presentacion_phtml --> src_cartaspresentacion_carta_presentacion_eliminar{{"/src/cartaspresentacion/carta_presentacion_eliminar"}}:::endpoint

    frontend_cartaspresentacion_controller_cartas_presentacion_ubis_lista_php --> src_cartaspresentacion_ubis_lista_data{{"/src/cartaspresentacion/ubis_lista_data"}}:::endpoint
    frontend_cartaspresentacion_controller_cartas_presentacion_form_php --> src_cartaspresentacion_carta_presentacion_form_data{{"/src/cartaspresentacion/carta_presentacion_form_data"}}:::endpoint
    frontend_cartaspresentacion_controller_cartas_presentacion_form_php --> frontend_cartaspresentacion_view_cartas_presentacion_form_phtml[["cartas_presentacion_form.phtml"]]:::vista
    frontend_cartaspresentacion_view_cartas_presentacion_form_phtml --> src_cartaspresentacion_carta_presentacion_update
```
