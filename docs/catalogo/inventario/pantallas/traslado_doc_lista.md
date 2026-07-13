---
id: "inventario.pantalla.traslado_doc_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Traslado — lista y guardar"
controller: "frontend/inventario/controller/traslado_doc_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_ctr"]
capacidades: ["inventario.lista_docs_de_ctr.gestionar"]
campos: ["post.id_lugar", "post.id_ubi"]
acciones: ["fnjs_selectAll"]
estado_revision: "revisado"
---

# Traslado — lista y guardar

Lista docs del centro/lugar y ejecuta `traslado_doc_guardar`.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/traslado_doc_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_de_ctr`

## Capacidades Relacionadas

- `inventario.lista_docs_de_ctr.gestionar`

## Campos Detectados

- `post.id_lugar`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_selectAll`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Lista docs del centro/lugar y ejecuta `traslado_doc_guardar`.

## Ruta de menú

- **Legacy:** sin entrada directa
- **Pills2:** —
