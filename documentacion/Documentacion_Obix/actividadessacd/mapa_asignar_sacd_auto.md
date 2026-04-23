```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef usecase fill:#bfb,stroke:#333,stroke-width:1px;
    classDef endpoint fill:#fdb,stroke:#333,stroke-width:1px;
    classDef wrapper fill:#eee,stroke:#999,stroke-width:1px,stroke-dasharray: 3 3;

    %% Entrada (menu)
    menu["Menu:<br/>frontend/actividadessacd/controller/asignar_sacd_auto.php"]:::controller
    wrap_legacy["Wrapper legacy:<br/>apps/actividadessacd/controller/asignar_sacd_auto.php"]:::wrapper
    wrap_legacy -->|require| menu

    %% Frontend controller + view
    menu --> view["View:<br/>frontend/actividadessacd/view/asignar_sacd_auto.phtml"]:::vista

    %% Interaccion desde el cliente (boton 'continuar')
    view -->|POST AJAX| endpoint_auto["Endpoint:<br/>/src/actividadessacd/sacd_asignar_auto"]:::endpoint

    %% Backend
    endpoint_auto --> uc["Use case:<br/>src/actividadessacd/application/SacdAsignarAuto"]:::usecase

    uc -->|1. seleccionar activ sr/sg<br/>status=ACTUAL, f_ini > inicurs_des| ActividadDlRepo[[ActividadDlRepository]]
    uc -->|2. filtrar sin cargo sacd| ActividadCargoRepo[[ActividadCargoRepository]]
    uc -->|3. centro encargado principal| CentroEncargadoRepo[[CentroEncargadoRepository]]
    uc -->|4. sacd titular del centro| EncargoRepo[[EncargoRepository + EncargoSacdRepository]]
    uc -->|5. guardar ActividadCargo observ='auto'| ActividadCargoRepo

    endpoint_auto -->|JSON {success, data:{asignadas, sin_asignar}}| view
```
