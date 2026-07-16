```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_notas_controller_asignaturas_pendientes_php(["asignaturas_pendientes.php"]):::controller --> apps_ubis_view_dl_rstgr_que_html_twig[["dl_rstgr_que.html.twig"]]:::vista
    apps_ubis_view_dl_rstgr_que_html_twig --> apps_notas_controller_asignaturas_pendientes_php(["asignaturas_pendientes.php"]):::controller
    apps_notas_controller_asignaturas_pendientes_php --> apps_notas_controller_asignaturas_pendientes_php(["asignaturas_pendientes.php"]):::controller
```