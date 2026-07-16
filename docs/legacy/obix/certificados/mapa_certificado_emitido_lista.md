```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_certificados_controller_certificado_emitido_lista_php(["certificado_emitido_lista.php"]):::controller --> frontend_certificados_view_certificado_emitido_lista_phtml[["certificado_emitido_lista.phtml"]]:::vista
    frontend_certificados_view_certificado_emitido_lista_phtml --> src_certificados_infrastructure_controllers_certificado_emitido_enviar_php(["certificado_emitido_enviar.php"]):::controller
    frontend_certificados_view_certificado_emitido_lista_phtml --> frontend_certificados_controller_certificado_emitido_upload_firmado_php(["certificado_emitido_upload_firmado.php"]):::controller
    frontend_certificados_view_certificado_emitido_lista_phtml --> frontend_certificados_controller_certificado_emitido_pdf_download_php(["certificado_emitido_pdf_download.php"]):::controller
    frontend_certificados_view_certificado_emitido_lista_phtml --> frontend_certificados_controller_certificado_emitido_ver_php(["certificado_emitido_ver.php"]):::controller
    frontend_certificados_view_certificado_emitido_lista_phtml --> src_certificados_infrastructure_controllers_certificado_emitido_delete_php(["certificado_emitido_delete.php"]):::controller
    frontend_certificados_view_certificado_emitido_lista_phtml --> frontend_certificados_controller_certificado_emitido_lista_php(["certificado_emitido_lista.php"]):::controller
    %% DESTÍ NO RESOLT des de frontend_certificados_view_certificado_emitido_lista_phtml: $oHash1->getCamposHtml() [DESTÍ NO RESOLT]
    frontend_certificados_controller_certificado_emitido_upload_firmado_php --> frontend_actividades_view_certificado_emitido_upload_firmado_html_twig[["certificado_emitido_upload_firmado.html.twig (NOT FOUND)"]]:::error
    frontend_certificados_controller_certificado_emitido_ver_php --> frontend_actividades_view_certificado_emitido_ver_html_twig[["certificado_emitido_ver.html.twig (NOT FOUND)"]]:::error
    class frontend_actividades_view_certificado_emitido_upload_firmado_html_twig,frontend_actividades_view_certificado_emitido_ver_html_twig error;
```