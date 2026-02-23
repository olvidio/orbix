```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_casas_controller_grupo_lista_php(["grupo_lista.php"]):::controller --> apps_casas_view_grupo_lista_html_twig[["grupo_lista.html.twig"]]:::vista
    apps_casas_view_grupo_lista_html_twig --> apps_casas_controller_grupo_ajax_php(["grupo_ajax.php"]):::controller
    apps_casas_view_grupo_lista_html_twig --> apps_casas_controller_grupo_lista_php(["grupo_lista.php"]):::controller
    apps_casas_view_grupo_lista_html_twig --> apps_casas_controller_grupo_form_php(["grupo_form.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_casas_view_grupo_lista_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
    apps_casas_controller_grupo_form_php(["grupo_form.php"]):::controller --> apps_casas_view_grupo_form_html_twig[["grupo_form.html.twig"]]:::vista
    apps_casas_view_grupo_form_html_twig --> apps_casas_controller_grupo_ajax_php(["grupo_ajax.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_casas_view_grupo_form_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
```