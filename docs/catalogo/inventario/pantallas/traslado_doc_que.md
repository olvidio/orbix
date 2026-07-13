---
id: "inventario.pantalla.traslado_doc_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Traslado de documentos — filtro"
controller: "frontend/inventario/controller/traslado_doc_que.php"
vistas: ["frontend/inventario/view/traslado_doc_que.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/traslado_doc_lista.php"]
endpoints: ["/src/inventario/lista_de_ctr", "/src/inventario/lista_lugares_de_ubi"]
capacidades: ["inventario.lista_de_ctr.gestionar", "inventario.lista_lugares_de_ubi.gestionar"]
campos: ["form.id_ubi", "form.id_ubi_new", "form.sel", "html.ok"]
acciones: ["fnjs_busca_docs", "fnjs_busca_lugares", "fnjs_busca_lugares_destino", "fnjs_busca_lugares_origen", "fnjs_crearSelectDesdeJson", "fnjs_guardar", "fnjs_put_desplegable_lugares"]
estado_revision: "revisado"
---

# Traslado de documentos — filtro

Selecciona centro origen y lugar; continúa a lista para mover docs.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/traslado_doc_que.php`

## Vistas Relacionadas

- `frontend/inventario/view/traslado_doc_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/traslado_doc_lista.php`

## Endpoints Usados

- `/src/inventario/lista_de_ctr`
- `/src/inventario/lista_lugares_de_ubi`

## Capacidades Relacionadas

- `inventario.lista_de_ctr.gestionar`
- `inventario.lista_lugares_de_ubi.gestionar`

## Campos Detectados

- `form.id_ubi`
- `form.id_ubi_new`
- `form.sel`
- `html.ok`

## Acciones Detectadas

- `fnjs_busca_docs`
- `fnjs_busca_lugares`
- `fnjs_busca_lugares_destino`
- `fnjs_busca_lugares_origen`
- `fnjs_crearSelectDesdeJson`
- `fnjs_guardar`
- `fnjs_put_desplegable_lugares`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Selecciona centro origen y lugar; continúa a lista para mover docs.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** —
