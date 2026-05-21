---
id: "inventario.pantalla.doc_asignado"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Doc Asignado"
controller: "frontend/inventario/controller/doc_asignado.php"
vistas: ["frontend/inventario/view/doc_asignado.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_asignados_por_tipo"]
capacidades: ["inventario.lista_docs_asignados_por_tipo.gestionar"]
campos: ["form.id_tipo_doc", "post.id_tipo_doc", "post.inventario"]
acciones: []
estado_revision: "generado"
---

# Doc Asignado

Descripcion funcional pendiente de revisar.

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
