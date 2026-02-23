```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_usuarios_controller_borrar_todos_pwd_php(["borrar_todos_pwd.php"]):::controller --> frontend__usuarios__view_borrar_todos_pwd_phtml[["borrar_todos_pwd.phtml"]]:::vista
    %% DESTÍ NO RESOLT des de frontend__usuarios__view_borrar_todos_pwd_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
```