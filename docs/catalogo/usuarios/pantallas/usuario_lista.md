---
id: "usuarios.pantalla.usuario_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "usuarios"
nombre: "Usuario Lista"
controller: "frontend/usuarios/controller/usuario_lista.php"
vistas: ["frontend/usuarios/view/usuario_lista.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_lista.php"]
endpoints: ["/src/usuarios/usuario_eliminar", "/src/usuarios/usuario_lista"]
capacidades: ["usuarios.usuario.gestionar"]
campos: ["form.sel", "form.username", "html.btn_ok", "post.id_sel", "post.scroll_id", "post.stack", "post.username"]
acciones: ["fnjs_actualizar", "fnjs_buscar", "fnjs_eliminar", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Usuario Lista

Listado principal de usuarios web con filtro por login, alta/edición y borrado.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/usuarios/controller/usuario_lista.php`

## Vistas Relacionadas

- `frontend/usuarios/view/usuario_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/usuario_form.php`
- `frontend/usuarios/controller/usuario_lista.php`

## Endpoints Usados

- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_lista`

## Capacidades Relacionadas

- `usuarios.usuario.gestionar`

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
- `fnjs_solo_uno`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sistema > usuarios web > lista usuarios
- **Pills2:** ADMIN LOCAL > usuarios web > lista usuarios
