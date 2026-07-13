---
id: "usuarios.pantalla.grupo_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "usuarios"
nombre: "Grupo Lista"
controller: "frontend/usuarios/controller/grupo_lista.php"
vistas: ["frontend/usuarios/view/grupo_lista.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/grupo_form.php", "frontend/usuarios/controller/grupo_lista.php"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_lista"]
capacidades: ["usuarios.grupo.gestionar"]
campos: ["form.sel", "form.username", "html.btn_ok", "post.id_sel", "post.scroll_id", "post.stack", "post.username"]
acciones: ["fnjs_actualizar", "fnjs_buscar", "fnjs_eliminar", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Grupo Lista

Listado de grupos de permisos (id ~ ^5) con alta/edición/borrado.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/usuarios/controller/grupo_lista.php`

## Vistas Relacionadas

- `frontend/usuarios/view/grupo_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/grupo_form.php`
- `frontend/usuarios/controller/grupo_lista.php`

## Endpoints Usados

- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_lista`

## Capacidades Relacionadas

- `usuarios.grupo.gestionar`

## Campos Detectados

- `form.sel`
- `form.username`
- `html.btn_ok`
- `post.id_sel`
- `post.scroll_id`
- `post.stack`
- `post.username`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sistema > usuarios web > grupos
- **Pills2:** ADMIN LOCAL > usuarios web > grupos
