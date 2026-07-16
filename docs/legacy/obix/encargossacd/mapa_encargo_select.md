```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_encargossacd_controller_encargo_select_php(["encargo_select.php"]):::controller --> apps_encargossacd_view_encargo_select_html_twig[["encargo_select.html.twig"]]:::vista
    apps_encargossacd_view_encargo_select_html_twig --> apps_encargossacd_controller_encargo_ajax_php(["encargo_ajax.php"]):::controller
    apps_encargossacd_view_encargo_select_html_twig --> apps_encargossacd_controller_encargo_select_php(["encargo_select.php"]):::controller
    apps_encargossacd_view_encargo_select_html_twig --> apps_encargossacd_controller_encargo_ver_php(["encargo_ver.php"]):::controller
    apps_encargossacd_view_encargo_select_html_twig --> apps_encargossacd_controller_encargo_horario_select_php(["encargo_horario_select.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_encargossacd_view_encargo_select_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
    apps_encargossacd_controller_encargo_ver_php(["encargo_ver.php"]):::controller --> apps_encargossacd_view_encargo_ver_html_twig[["encargo_ver.html.twig"]]:::vista
    apps_encargossacd_view_encargo_ver_html_twig --> apps_encargossacd_controller_encargo_ajax_php(["encargo_ajax.php"]):::controller
    apps_encargossacd_view_encargo_ver_html_twig --> apps_encargossacd_controller_ctr_get_select_php(["ctr_get_select.php"]):::controller
    apps_encargossacd_view_encargo_ver_html_twig --> apps_encargossacd_controller_zonas_get_select_php(["zonas_get_select.php"]):::controller
    apps_encargossacd_view_encargo_ver_html_twig --> apps_encargossacd_controller_encargo_ver_php(["encargo_ver.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_encargossacd_view_encargo_ver_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
    apps_encargossacd_controller_encargo_horario_select_php(["encargo_horario_select.php"]):::controller --> apps_encargossacd_view_encargo_horario_select_html_twig[["encargo_horario_select.html.twig"]]:::vista
    apps_encargossacd_view_encargo_horario_select_html_twig --> des_tareas_horario_ver_php(["horario_ver.php"]):::controller
    apps_encargossacd_view_encargo_horario_select_html_twig --> des_tareas_horario_update_php(["horario_update.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_encargossacd_view_encargo_horario_select_html_twig: {{ div_para_nuevo }} [DESTÍ NO RESOLT]
    %% DESTÍ NO RESOLT des de apps_encargossacd_view_encargo_horario_select_html_twig: {{ origen }} [DESTÍ NO RESOLT]
    class des_tareas_horario_ver_php,des_tareas_horario_update_php error;
```