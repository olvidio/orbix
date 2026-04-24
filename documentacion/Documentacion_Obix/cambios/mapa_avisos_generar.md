```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef backend fill:#bfb,stroke:#333,stroke-width:1px;

    frontend_cambios_controller_avisos_generar_php(["frontend/cambios/controller/avisos_generar.php"]):::controller --> frontend_cambios_view_avisos_generar_phtml[["frontend/cambios/view/avisos_generar.phtml"]]:::vista
    frontend_cambios_controller_avisos_generar_php --> src_cambios_avisos_generar_lista_data[["/src/cambios/avisos_generar_lista_data"]]:::backend
```

> El legacy usaba dos vistas Twig (`avisos_generar_condicion.html.twig` y
> `avisos_generar_lista.html.twig` en `apps/cambios/view/`). Ambas han
> sido eliminadas tras la migracion a `frontend/` (vertical slice 2).
