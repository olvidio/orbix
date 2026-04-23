```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef api fill:#bfb,stroke:#333,stroke-width:2px;
    classDef usecase fill:#ffd,stroke:#333,stroke-width:1px;

    frontend_controller(["frontend/actividadescentro/controller/activ_ctr.php"]):::controller --> frontend_view[["frontend/actividadescentro/view/activ_ctr.phtml"]]:::vista

    frontend_view -- AJAX JSON --> src_lista(["/src/actividadescentro/lista_actividades_ctr_data"]):::api
    frontend_view -- AJAX JSON --> src_encargados(["/src/actividadescentro/centros_encargados_data"]):::api
    frontend_view -- AJAX JSON --> src_disponibles(["/src/actividadescentro/centros_disponibles_data"]):::api
    frontend_view -- AJAX JSON --> src_asignar(["/src/actividadescentro/centro_encargado_asignar"]):::api
    frontend_view -- AJAX JSON --> src_reordenar(["/src/actividadescentro/centro_encargado_reordenar"]):::api
    frontend_view -- AJAX JSON --> src_eliminar(["/src/actividadescentro/centro_encargado_eliminar"]):::api

    src_lista --> uc_lista[[ListaActividadesCtrData]]:::usecase
    src_encargados --> uc_encargados[[CentrosEncargadosData]]:::usecase
    src_disponibles --> uc_disponibles[[CentrosDisponiblesData]]:::usecase
    src_asignar --> uc_asignar[[CentroEncargadoAsignar]]:::usecase
    src_reordenar --> uc_reordenar[[CentroEncargadoReordenar]]:::usecase
    src_eliminar --> uc_eliminar[[CentroEncargadoEliminar]]:::usecase

    apps_legacy(["apps/actividadescentro/controller/activ_ctr.php (wrapper legacy)"]):::controller --> frontend_controller
```

La entrada canonica es `frontend/actividadescentro/controller/activ_ctr.php`. El
wrapper legacy en `apps/` solo existe por compatibilidad con entradas de menu
en BD que todavia apuntan a `apps/...`; redirige al controlador frontend.

Todos los endpoints backend viven en `src/actividadescentro/config/routes.php`
bajo el prefijo `/src/actividadescentro/` y responden JSON con el contrato
estandar `{success: bool, mensaje: string, data: string|array}`.
