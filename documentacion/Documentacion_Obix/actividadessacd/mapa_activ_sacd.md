```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividadessacd_controller_activ_sacd_php(["activ_sacd.php"]):::controller --> apps_actividadessacd_view_activ_sacd_html_twig[["activ_sacd.html.twig"]]:::vista
    apps_actividadessacd_view_activ_sacd_html_twig --> apps_actividadessacd_controller_activ_sacd_ajax_php(["activ_sacd_ajax.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_actividadessacd_view_activ_sacd_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
```