```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_notas_controller_acta_select_php(["acta_select.php"]):::controller --> apps_notas_view_acta_select_phtml[["acta_select.phtml"]]:::vista
    apps_notas_view_acta_select_phtml --> apps_notas_controller_acta_pdf_download_php(["acta_pdf_download.php"]):::controller
    apps_notas_view_acta_select_phtml --> apps_notas_controller_acta_imprimir_php(["acta_imprimir.php"]):::controller
    apps_notas_view_acta_select_phtml --> apps_notas_controller_acta_ver_php(["acta_ver.php"]):::controller
    apps_notas_view_acta_select_phtml --> apps_notas_controller_acta_update_php(["acta_update.php"]):::controller
    apps_notas_view_acta_select_phtml --> apps_notas_controller_acta_select_php(["acta_select.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_notas_view_acta_select_phtml: $oHash1->getCamposHtml(); [DESTÍ NO RESOLT]
    apps_notas_controller_acta_imprimir_php(["acta_imprimir.php"]):::controller --> apps_notas_view_acta_imprimir_phtml[["acta_imprimir.phtml"]]:::vista
    apps_notas_view_acta_imprimir_phtml --> apps_notas_controller_acta_imprimir_php(["acta_imprimir.php"]):::controller
    apps_notas_controller_acta_ver_php(["acta_ver.php"]):::controller --> apps_notas_view_acta_ver_phtml[["acta_ver.phtml"]]:::vista
    apps_notas_view_acta_ver_phtml --> apps_notas_controller_acta_ver_php(["acta_ver.php"]):::controller
    apps_notas_view_acta_ver_phtml --> apps_notas_controller_acta_update_php(["acta_update.php"]):::controller
    apps_notas_view_acta_ver_phtml --> apps_notas_controller_acta_pdf_upload_php(["acta_pdf_upload.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_notas_view_acta_ver_phtml: $oHashActa->getCamposHtml(); [DESTÍ NO RESOLT]
    %% DESTÍ NO RESOLT des de apps_notas_view_acta_ver_phtml: $oHashActaPdf->getCamposHtml(); [DESTÍ NO RESOLT]
    apps_notas_view_acta_ver_phtml --> apps_notas_controller_acta_pdf_delete_php(["acta_pdf_delete.php"]):::controller
    apps_notas_view_acta_ver_phtml --> apps_notas_controller_acta_ajax_php(["acta_ajax.php"]):::controller
```