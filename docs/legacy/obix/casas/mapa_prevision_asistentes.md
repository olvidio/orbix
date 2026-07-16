```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_casas_controller_prevision_asistentes_php(["prevision_asistentes.php"]):::controller --> apps_casas_view_prevision_asistentes_html_twig[["prevision_asistentes.html.twig"]]:::vista
    apps_casas_view_prevision_asistentes_html_twig --> apps_casas_controller_prevision_asistentes_php(["prevision_asistentes.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_casas_view_prevision_asistentes_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
```