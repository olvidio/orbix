---
id: "inventario.pantalla.equipajes_movimientos_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Movimientos maletas — filtro"
controller: "frontend/inventario/controller/equipajes_movimientos_que.php"
vistas: ["frontend/inventario/view/equipajes_movimientos_que.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/equipajes_movimientos.php"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
campos: ["form.sel"]
acciones: ["fnjs_lista_docs", "fnjs_ver_movimientos"]
estado_revision: "revisado"
---

# Movimientos maletas — filtro

Elige fecha y equipajes para comparar movimientos.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_movimientos_que.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_movimientos_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/equipajes_movimientos.php`

## Endpoints Usados

- `/src/inventario/lista_equipajes_desde_fecha`

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Campos Detectados

- `form.sel`

## Acciones Detectadas

- `fnjs_lista_docs`
- `fnjs_ver_movimientos`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Elige fecha y equipajes para comparar movimientos.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** —
