---
id: "menus.pantalla.grupmenu_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Grupmenu Form"
controller: "frontend/menus/controller/grupmenu_form.php"
vistas: ["frontend/menus/view/grupmenu_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/menus/grupmenu_info"]
capacidades: ["menus.grupmenu_info.gestionar"]
campos: ["form.grupmenu", "form.orden", "form.que", "post.id_grupmenu", "post.que", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_cancelar", "fnjs_guardar"]
estado_revision: "generado"
---

# Grupmenu Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/menus/controller/grupmenu_form.php`

## Vistas Relacionadas

- `frontend/menus/view/grupmenu_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/menus/grupmenu_info`

## Capacidades Relacionadas

- `menus.grupmenu_info.gestionar`

## Campos Detectados

- `form.grupmenu`
- `form.orden`
- `form.que`
- `post.id_grupmenu`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
