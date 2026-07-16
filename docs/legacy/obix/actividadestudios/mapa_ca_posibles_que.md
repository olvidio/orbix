```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_actividadestudios_controller_ca_posibles_que_php(["ca_posibles_que.php"]):::controller --> apps_actividadestudios_view_ca_posibles_que_phtml[["ca_posibles_que.phtml"]]:::vista
    apps_actividadestudios_view_ca_posibles_que_phtml --> apps_actividadestudios_controller_ca_posibles_php(["ca_posibles.php"]):::controller
    %% DESTÍ NO RESOLT des de apps_actividadestudios_view_ca_posibles_que_phtml: $oHash->getCamposHtml(); [DESTÍ NO RESOLT]
    apps_actividadestudios_controller_ca_posibles_php(["ca_posibles.php"]):::controller --> apps_actividadestudios_view_ca_posibles_lista_phtml[["ca_posibles_lista.phtml"]]:::vista
    apps_actividadestudios_view_ca_posibles_lista_phtml --> apps_dossiers_controller_dossiers_ver_php(["dossiers_ver.php"]):::controller
```