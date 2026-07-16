```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividades_controller_actividad_ver_php(["actividad_ver.php"]):::controller --> apps_actividades_view_actividad_form_html_twig[["actividad_form.html.twig"]]:::vista
```