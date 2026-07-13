---
id: "inventario.pantalla.inventario_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Elegir inventario centros o DLB"
controller: "frontend/inventario/controller/inventario_que.php"
vistas: ["frontend/inventario/view/inventario_que.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/doc_de_ctr.php", "frontend/inventario/controller/doc_de_dlb.php"]
endpoints: []
capacidades: []
campos: ["html.okay", "html.okay2"]
acciones: ["fnjs_enviar_formulario", "fnjs_go", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Elegir inventario centros o DLB

Punto de entrada: el usuario elige inventario de centros o de DLB/casa; redirige a `doc_de_ctr` o `doc_de_dlb`.


## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/inventario/controller/inventario_que.php`

## Vistas Relacionadas

- `frontend/inventario/view/inventario_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/doc_de_ctr.php`
- `frontend/inventario/controller/doc_de_dlb.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `html.okay`
- `html.okay2`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_go`
- `fnjs_left_side_hide`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Punto de entrada: el usuario elige inventario de centros o de DLB/casa; redirige a `doc_de_ctr` o `doc_de_dlb`.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —
