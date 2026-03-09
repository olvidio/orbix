```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_asistentes_controller_que_ctr_lista_php(["que_ctr_lista.php"]):::controller --> apps_asistentes_view_que_ctr_lista_phtml[["que_ctr_lista.phtml"]]:::vista
    apps_asistentes_view_que_ctr_lista_phtml --> programas_sm_agd_lista_profesion_php(["lista_profesion.php"]):::controller
    class programas_sm_agd_lista_profesion_php error;
```