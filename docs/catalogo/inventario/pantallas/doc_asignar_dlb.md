---
id: "inventario.pantalla.doc_asignar_dlb"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Doc Asignar Dlb"
controller: "frontend/inventario/controller/doc_asignar_dlb.php"
vistas: ["frontend/inventario/view/doc_asignar_dlb.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/doc_asignar_dlb_guardar", "/src/inventario/lista_docs_asignar_dlb"]
capacidades: ["inventario.doc_asignar_dlb.gestionar", "inventario.lista_docs_asignar_dlb.gestionar"]
campos: ["html.f_asignado", "html.f_recibido", "html.okay", "post.id_tipo_doc", "post.sel"]
acciones: ["fnjs_guardar"]
estado_revision: "generado"
---

# Doc Asignar Dlb

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_asignar_dlb.php`

## Vistas Relacionadas

- `frontend/inventario/view/doc_asignar_dlb.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/doc_asignar_dlb_guardar`
- `/src/inventario/lista_docs_asignar_dlb`

## Capacidades Relacionadas

- `inventario.doc_asignar_dlb.gestionar`
- `inventario.lista_docs_asignar_dlb.gestionar`

## Campos Detectados

- `html.f_asignado`
- `html.f_recibido`
- `html.okay`
- `post.id_tipo_doc`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
