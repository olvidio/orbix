---
id: "inventario.pantalla.equipajes_form_add"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Form Add"
controller: "frontend/inventario/controller/equipajes_form_add.php"
vistas: ["frontend/inventario/view/equipajes_form_add.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_tipo_doc"]
capacidades: ["inventario.lista_tipo_doc.gestionar"]
campos: ["form.id_tipo_doc", "form.sel", "post.id_equipaje", "post.id_grupo", "post.id_item_egm"]
acciones: ["fnjs_add_doc", "fnjs_cerrar", "fnjs_docs_libres"]
estado_revision: "generado"
---

# Equipajes Form Add

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_add.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_add.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_tipo_doc`

## Capacidades Relacionadas

- `inventario.lista_tipo_doc.gestionar`

## Campos Detectados

- `form.id_tipo_doc`
- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`

## Acciones Detectadas

- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_docs_libres`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
