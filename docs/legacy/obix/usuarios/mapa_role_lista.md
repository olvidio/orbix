```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_usuarios_controller_role_lista_php(["role_lista.php"]):::controller --> frontend_usuarios_view_role_lista_phtml[["role_lista.phtml"]]:::vista
    frontend_usuarios_view_role_lista_phtml --> frontend_usuarios_controller_role_form_php(["role_form.php"]):::controller
    frontend_usuarios_view_role_lista_phtml --> frontend_usuarios_controller_role_lista_php(["role_lista.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_role_lista_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
    frontend_usuarios_controller_role_form_php(["role_form.php"]):::controller --> frontend_usuarios_view_role_form_phtml[["role_form.phtml"]]:::vista
    frontend_usuarios_view_role_form_phtml --> frontend_usuarios_controller_role_form_php(["role_form.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_role_form_phtml: $oHash->getCamposHtml(); [DESTÍ NO RESOLT]
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_role_form_phtml: $oHash1->getCamposHtml(); [DESTÍ NO RESOLT]
```