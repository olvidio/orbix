```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    frontend_inventario_controller_inventario_que_php(["inventario_que.php"]):::controller --> frontend_inventario_view_inventario_que_phtml[["inventario_que.phtml"]]:::vista
    %% DESTÍ NO RESOLT des de frontend_inventario_view_inventario_que_phtml: $oHash->getCamposHtml() [DESTÍ NO RESOLT]
```