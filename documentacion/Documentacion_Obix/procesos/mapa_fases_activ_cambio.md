```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_procesos_controller_fases_activ_cambio_php(["fases_activ_cambio.php"]):::controller --> apps_procesos_view_fases_activ_cambio_html_twig[["fases_activ_cambio.html.twig"]]:::vista
    apps_procesos_view_fases_activ_cambio_html_twig --> apps_procesos_controller_actividad_proceso_php(["actividad_proceso.php"]):::controller
    apps_procesos_view_fases_activ_cambio_html_twig --> apps_procesos_controller_fases_activ_cambio_ajax_php(["fases_activ_cambio_ajax.php"]):::controller
    apps_procesos_controller_actividad_proceso_php(["actividad_proceso.php"]):::controller --> apps_procesos_view_actividad_proceso_html_twig[["actividad_proceso.html.twig"]]:::vista
    apps_procesos_view_actividad_proceso_html_twig --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
    apps_procesos_view_actividad_proceso_html_twig --> apps_procesos_controller_actividad_proceso_ajax_php(["actividad_proceso_ajax.php"]):::controller
```