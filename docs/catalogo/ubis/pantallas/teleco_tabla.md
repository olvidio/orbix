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
estado_revision: "revisado"
---

# Teleco Tabla

Tabla AJAX de telecomunicaciones de un ubi con acciones modificar y eliminar.

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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
