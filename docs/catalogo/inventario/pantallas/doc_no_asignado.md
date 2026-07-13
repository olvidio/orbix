---
id: "inventario.pantalla.doc_no_asignado"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Centros sin documento"
controller: "frontend/inventario/controller/doc_no_asignado.php"
vistas: ["frontend/inventario/view/doc_no_asignado.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_no_asignados_por_tipo"]
capacidades: ["inventario.lista_docs_no_asignados_por_tipo.gestionar"]
campos: ["form.id_tipo_doc", "post.id_tipo_doc", "post.inventario"]
acciones: []
estado_revision: "revisado"
---

# Centros sin documento

Centros pendientes de recibir el tipo doc.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_no_asignado.php`

## Vistas Relacionadas

- `frontend/inventario/view/doc_no_asignado.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_no_asignados_por_tipo`

## Capacidades Relacionadas

- `inventario.lista_docs_no_asignados_por_tipo.gestionar`

## Campos Detectados

- `form.id_tipo_doc`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Centros pendientes de recibir el tipo doc.

## Ruta de menú

- **Legacy:** sin entrada directa
- **Pills2:** —
