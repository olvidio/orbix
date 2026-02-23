```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_pasarela_controller_exportar_que_php(["exportar_que.php"]):::controller --> apps_pasarela_view_exportar_que_html_twig[["exportar_que.html.twig"]]:::vista
    apps_pasarela_view_exportar_que_html_twig --> apps_pasarela_controller_exportar_select_php(["exportar_select.php"]):::controller
```