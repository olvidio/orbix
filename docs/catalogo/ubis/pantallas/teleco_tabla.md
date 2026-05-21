---
id: "ubis.pantalla.teleco_tabla"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Teleco Tabla"
controller: "frontend/ubis/controller/teleco_tabla.php"
vistas: ["frontend/ubis/view/teleco_tabla.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/teleco_editar.php", "frontend/ubis/controller/teleco_tabla.php"]
endpoints: ["/src/ubis/teleco_tabla"]
capacidades: ["ubis.teleco_tabla.gestionar"]
campos: ["form.mod", "form.sel", "html.btn_new", "html.mod", "html.refresh", "post.id_ubi", "post.obj_pau"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_modificar", "fnjs_nuevo", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Teleco Tabla

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/teleco_tabla.php`

## Vistas Relacionadas

- `frontend/ubis/view/teleco_tabla.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/teleco_editar.php`
- `frontend/ubis/controller/teleco_tabla.php`

## Endpoints Usados

- `/src/ubis/teleco_tabla`

## Capacidades Relacionadas

- `ubis.teleco_tabla.gestionar`

## Campos Detectados

- `form.mod`
- `form.sel`
- `html.btn_new`
- `html.mod`
- `html.refresh`
- `post.id_ubi`
- `post.obj_pau`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
