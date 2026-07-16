```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_usuarios_controller_grupo_lista_php(["grupo_lista.php"]):::controller --> frontend_usuarios_view_grupo_lista_phtml[["grupo_lista.phtml"]]:::vista
    frontend_usuarios_view_grupo_lista_phtml --> frontend_usuarios_controller_grupo_form_php(["grupo_form.php"]):::controller
    frontend_usuarios_view_grupo_lista_phtml --> frontend_usuarios_controller_grupo_lista_php(["grupo_lista.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_grupo_lista_phtml: $oHashSelect->getCamposHtml() [DESTÍ NO RESOLT]
    frontend_usuarios_controller_grupo_form_php(["grupo_form.php"]):::controller --> frontend_usuarios_view_grupo_form_phtml[["grupo_form.phtml"]]:::vista
    frontend_usuarios_view_grupo_form_phtml --> src_usuarios_infrastructure_controllers_grupo_guardar_php(["grupo_guardar.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_usuarios_view_grupo_form_phtml: $oHashG->getCamposHtml(); [DESTÍ NO RESOLT]
```