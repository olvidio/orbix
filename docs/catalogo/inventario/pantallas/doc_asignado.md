---
id: "inventario.pantalla.doc_asignado"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Documentos ya asignados"
controller: "frontend/inventario/controller/doc_asignado.php"
vistas: ["frontend/inventario/view/doc_asignado.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_asignados_por_tipo"]
capacidades: ["inventario.lista_docs_asignados_por_tipo.gestionar"]
campos: ["form.id_tipo_doc", "post.id_tipo_doc", "post.inventario"]
acciones: []
estado_revision: "revisado"
---

# Documentos ya asignados

Lista centros con el tipo doc asignado.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_asignado.php`

## Vistas Relacionadas

- `frontend/inventario/view/doc_asignado.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_asignados_por_tipo`

## Capacidades Relacionadas

- `inventario.lista_docs_asignados_por_tipo.gestionar`

## Campos Detectados

- `form.id_tipo_doc`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Lista centros con el tipo doc asignado.

## Ruta de menú

- **Legacy:** sin entrada directa (desde docs_asignar_que)
- **Pills2:** —
