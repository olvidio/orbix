---
id: "inventario.pantalla.equipajes_form_del"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Form Del"
controller: "frontend/inventario/controller/equipajes_form_del.php"
vistas: ["frontend/inventario/view/equipajes_form_del.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_egm"]
capacidades: ["inventario.lista_docs_de_egm.gestionar"]
campos: ["form.sel", "post.id_equipaje", "post.id_grupo", "post.id_item_egm"]
acciones: ["fnjs_cerrar", "fnjs_del_doc"]
estado_revision: "generado"
---

# Equipajes Form Del

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_del.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_del.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_de_egm`

## Capacidades Relacionadas

- `inventario.lista_docs_de_egm.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_del_doc`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
