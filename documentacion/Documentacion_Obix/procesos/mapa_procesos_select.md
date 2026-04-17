```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_procesos_controller_procesos_select_php(["procesos_select.php"]):::controller --> frontend_procesos_view_procesos_select_html_twig[["procesos_select.html.twig"]]:::vista
    frontend_procesos_controller_procesos_select_php --> src_procesos_procesos_select_data_php(["/src/procesos/procesos_select_data"]):::controller
    frontend_procesos_view_procesos_select_html_twig --> apps_procesos_controller_procesos_ajax_php(["procesos_ajax.php (legacy, slice 2)"]):::controller
    frontend_procesos_view_procesos_select_html_twig --> apps_procesos_controller_procesos_ver_php(["procesos_ver.php (legacy, slice 2)"]):::controller
    apps_procesos_controller_procesos_ver_php(["procesos_ver.php"]):::controller --> apps_procesos_view_procesos_ver_html_twig[["procesos_ver.html.twig"]]:::vista
    apps_procesos_view_procesos_ver_html_twig --> apps_procesos_controller_procesos_ajax_php(["procesos_ajax.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_procesos_view_procesos_ver_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
```