```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_usuarios_controller_usuario_lista_php(["usuario_lista.php"]):::controller --> frontend_usuarios_view_usuario_lista_phtml[["usuario_lista.phtml"]]:::vista
    frontend_usuarios_view_usuario_lista_phtml --> frontend_usuarios_controller_usuario_lista_php(["usuario_lista.php"]):::controller
    frontend_usuarios_view_usuario_lista_phtml --> frontend_usuarios_controller_usuario_form_php(["usuario_form.php"]):::controller
    frontend_usuarios_view_usuario_lista_phtml --> src_usuarios_infrastructure_controllers_usuario_eliminar_php(["usuario_eliminar.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_usuario_lista_phtml: $oHash1->getCamposHtml() [DESTÍ NO RESOLT]
    frontend_usuarios_controller_usuario_form_php(["usuario_form.php"]):::controller --> frontend_usuarios_view_usuario_form_phtml[["usuario_form.phtml"]]:::vista
    frontend_usuarios_view_usuario_form_phtml --> src_usuarios_infrastructure_controllers_usuario_check_pwd_php(["usuario_check_pwd.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_usuario_form_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_usuario_form_phtml: $url_usuario_guardar [DESTÍ NO RESOLT]
```