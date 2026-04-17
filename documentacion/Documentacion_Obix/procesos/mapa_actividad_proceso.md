```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;

    frontend_procesos_controller_actividad_proceso_php(["actividad_proceso.php"]):::controller --> frontend_procesos_view_actividad_proceso_html_twig[["actividad_proceso.html.twig"]]:::vista
    frontend_procesos_controller_actividad_proceso_php --> src_procesos_actividad_proceso_data(["/src/procesos/actividad_proceso_data"]):::controller
    frontend_procesos_view_actividad_proceso_html_twig --> src_procesos_actividad_proceso_ajax(["/src/procesos/actividad_proceso_ajax"]):::controller
    frontend_procesos_view_actividad_proceso_html_twig --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
```
