---
id: "menus.pantalla.grupmenu_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Grupmenu Lista"
controller: "frontend/menus/controller/grupmenu_lista.php"
vistas: ["frontend/menus/view/grupmenu_lista.phtml"]
fragmentos_frontend: ["frontend/menus/controller/grupmenu_form.php", "frontend/menus/controller/grupmenu_lista.php"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_lista"]
capacidades: ["menus.grupmenu.gestionar"]
campos: ["form.sel", "post.filtro_grupo", "post.id_menu", "post.nuevo"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "generado"
---

# Grupmenu Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/menus/controller/grupmenu_lista.php`

## Vistas Relacionadas

- `frontend/menus/view/grupmenu_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/menus/controller/grupmenu_form.php`
- `frontend/menus/controller/grupmenu_lista.php`

## Endpoints Usados

- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_lista`

## Capacidades Relacionadas

- `menus.grupmenu.gestionar`

## Campos Detectados

- `form.sel`
- `post.filtro_grupo`
- `post.id_menu`
- `post.nuevo`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
