```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_devel_db_admin_controller_db_absorber_esquema_que_php(["db_absorber_esquema_que.php"]):::controller --> frontend_devel_db_admin_view_db_absorber_esquema_que_phtml[["db_absorber_esquema_que.phtml"]]:::vista
    frontend_devel_db_admin_view_db_absorber_esquema_que_phtml --> frontend_devel_db_admin_controller_db_absorber_esquema_php(["db_absorber_esquema.php"]):::controller
```
