---
id: "usuarios.pantalla.role_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "usuarios"
nombre: "Role Lista"
controller: "frontend/usuarios/controller/role_lista.php"
vistas: ["frontend/usuarios/view/role_lista.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/role_form.php", "frontend/usuarios/controller/role_lista.php"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_lista"]
capacidades: ["usuarios.role.gestionar"]
campos: ["form.sel", "post.id_sel", "post.scroll_id", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Role Lista

Listado de roles con grupmenus asociados; CRUD según permiso superadmin/admin.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/usuarios/controller/role_lista.php`

## Vistas Relacionadas

- `frontend/usuarios/view/role_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/role_form.php`
- `frontend/usuarios/controller/role_lista.php`

## Endpoints Usados

- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_lista`

## Capacidades Relacionadas

- `usuarios.role.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_sel`
- `post.scroll_id`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sistema > usuarios web > lista de roles
- **Pills2:** ADMIN LOCAL > usuarios web > lista de roles
