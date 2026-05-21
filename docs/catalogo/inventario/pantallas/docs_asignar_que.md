---
id: "inventario.pantalla.docs_asignar_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Docs Asignar Que"
controller: "frontend/inventario/controller/docs_asignar_que.php"
vistas: ["frontend/inventario/view/docs_asignar_que.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/doc_asignado.php", "frontend/inventario/controller/doc_de_ctr.php", "frontend/inventario/controller/doc_de_dlb.php", "frontend/inventario/controller/doc_no_asignado.php"]
endpoints: ["/src/inventario/lista_tipo_doc"]
capacidades: ["inventario.lista_tipo_doc.gestionar"]
campos: ["form.id_tipo_doc", "html.okay", "html.okay2", "html.okay3", "html.okay4", "post.id_tipo_doc", "post.inventario"]
acciones: ["fnjs_enviar_formulario", "fnjs_go", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Docs Asignar Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/docs_asignar_que.php`

## Vistas Relacionadas

- `frontend/inventario/view/docs_asignar_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/doc_asignado.php`
- `frontend/inventario/controller/doc_de_ctr.php`
- `frontend/inventario/controller/doc_de_dlb.php`
- `frontend/inventario/controller/doc_no_asignado.php`

## Endpoints Usados

- `/src/inventario/lista_tipo_doc`

## Capacidades Relacionadas

- `inventario.lista_tipo_doc.gestionar`

## Campos Detectados

- `form.id_tipo_doc`
- `html.okay`
- `html.okay2`
- `html.okay3`
- `html.okay4`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_go`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
