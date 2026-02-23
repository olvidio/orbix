```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_personas_controller_personas_select_php(["personas_select.php"]):::controller --> apps_personas_view_personas_select_phtml[["personas_select.phtml"]]:::vista
    apps_personas_view_personas_select_phtml --> apps_personas_controller_home_persona_php(["home_persona.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_personas_controller_traslado_form_php(["traslado_form.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_personas_controller_personas_editar_php(["personas_editar.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_notas_controller_tessera_ver_php(["tessera_ver.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_actividadessacd_controller_com_sacd_activ_php(["com_sacd_activ.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_actividadestudios_controller_ca_posibles_php(["ca_posibles.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_actividadplazas_controller_peticiones_activ_php(["peticiones_activ.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_notas_controller_tessera_imprimir_php(["tessera_imprimir.php"]):::controller
    apps_personas_view_personas_select_phtml --> frontend_certificados_controller_certificado_emitido_imprimir_php(["certificado_emitido_imprimir.php"]):::controller
    apps_personas_view_personas_select_phtml --> frontend_certificados_controller_certificado_recibido_adjuntar_php(["certificado_recibido_adjuntar.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_profesores_controller_ficha_profesor_stgr_php(["ficha_profesor_stgr.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_personas_controller_stgr_cambio_php(["stgr_cambio.php"]):::controller
    apps_personas_view_personas_select_phtml --> apps_notas_controller_tessera_copiar_select_php(["tessera_copiar_select.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_personas_view_personas_select_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
    apps_personas_controller_home_persona_php(["home_persona.php"]):::controller --> apps_personas_view_home_persona_phtml[["home_persona.phtml"]]:::vista
    apps_personas_view_home_persona_phtml --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
    apps_personas_view_home_persona_phtml --> apps_personas_controller_home_persona_php(["home_persona.php"]):::controller
    apps_personas_view_home_persona_phtml --> apps_personas_controller_personas_editar_php(["personas_editar.php"]):::controller
    apps_personas_controller_traslado_form_php(["traslado_form.php"]):::controller --> apps_personas_view_traslado_form_phtml[["traslado_form.phtml"]]:::vista
    apps_personas_view_traslado_form_phtml --> apps_personas_controller_traslado_update_php(["traslado_update.php"]):::controller
    apps_personas_view_traslado_form_phtml --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
    apps_personas_view_traslado_form_phtml --> apps_personas_controller_home_persona_php(["home_persona.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_personas_view_traslado_form_phtml: $oHash->getCamposHtml(); [DESTÍ NO RESOLT]
    apps_actividadessacd_controller_com_sacd_activ_php(["com_sacd_activ.php"]):::controller --> apps_actividadessacd_view_com_un_sacd_activ_print_phtml[["com_un_sacd_activ_print.phtml"]]:::vista
    apps_actividadessacd_view_com_un_sacd_activ_print_phtml --> apps_actividadessacd_controller_com_sacd_activ_php(["com_sacd_activ.php"]):::controller
    apps_actividadestudios_controller_ca_posibles_php(["ca_posibles.php"]):::controller --> apps_actividadestudios_view_ca_posibles_lista_phtml[["ca_posibles_lista.phtml"]]:::vista
    apps_actividadestudios_view_ca_posibles_lista_phtml --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
    apps_actividadplazas_controller_peticiones_activ_php(["peticiones_activ.php"]):::controller --> apps_actividadplazas_view_peticiones_activ_phtml[["peticiones_activ.phtml"]]:::vista
    apps_actividadplazas_view_peticiones_activ_phtml --> apps_actividadplazas_controller_peticiones_activ_ajax_php(["peticiones_activ_ajax.php"]):::controller
    apps_actividadplazas_view_peticiones_activ_phtml --> apps_actividadplazas_controller_peticiones_activ_php(["peticiones_activ.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_actividadplazas_view_peticiones_activ_phtml: $oHash->getCamposHtml(); [DESTÍ NO RESOLT]
    frontend_certificados_controller_certificado_emitido_imprimir_php --> frontend_actividades_view_certificado_emitido_imprimir_html_twig[["certificado_emitido_imprimir.html.twig (NOT FOUND)"]]:::error
    frontend_certificados_controller_certificado_recibido_adjuntar_php --> frontend_actividades_view_certificado_recibido_adjuntar_html_twig[["certificado_recibido_adjuntar.html.twig (NOT FOUND)"]]:::error
    apps_profesores_controller_ficha_profesor_stgr_php --> apps_actividades_view____view_ficha_profesor_stgr_print_phtml[["ficha_profesor_stgr.print.phtml (NOT FOUND)"]]:::error
    apps_personas_controller_stgr_cambio_php(["stgr_cambio.php"]):::controller --> apps_personas_view_stgr_cambio_phtml[["stgr_cambio.phtml"]]:::vista
    apps_personas_view_stgr_cambio_phtml --> apps_personas_controller_stgr_update_php(["stgr_update.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_personas_view_stgr_cambio_phtml: $oHash->getCamposHtml(); [DESTÍ NO RESOLT]
    apps_notas_controller_tessera_copiar_select_php(["tessera_copiar_select.php"]):::controller --> apps_notas_view_tessera_copiar_select_html_twig[["tessera_copiar_select.html.twig"]]:::vista
    apps_notas_view_tessera_copiar_select_html_twig --> apps_notas_controller_tessera_copiar_php(["tessera_copiar.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_notas_view_tessera_copiar_select_html_twig: {{ oHash }} [DESTÍ NO RESOLT]
    class frontend_actividades_view_certificado_emitido_imprimir_html_twig,frontend_actividades_view_certificado_recibido_adjuntar_html_twig,apps_actividades_view____view_ficha_profesor_stgr_print_phtml error;
```