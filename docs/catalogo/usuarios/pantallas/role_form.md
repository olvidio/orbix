---
id: "usuarios.pantalla.role_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Role Form"
controller: "frontend/usuarios/controller/role_form.php"
vistas: ["frontend/usuarios/view/role_form.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/role_form.php", "frontend/usuarios/controller/role_grupmenu.php"]
endpoints: ["/src/usuarios/role_grupmenu_del", "/src/usuarios/role_guardar", "/src/usuarios/role_info"]
capacidades: ["usuarios.role.gestionar", "usuarios.role_grupmenu_del.gestionar", "usuarios.role_info.gestionar"]
campos: ["form.dmz", "form.pau", "form.que", "form.role", "form.sel", "form.sf", "form.sv", "html.dmz", "html.que", "html.role", "html.sf", "html.sv", "post.id_role", "post.que", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_add_grupmenu", "fnjs_del_grupmenu", "fnjs_enviar_formulario", "fnjs_guardar", "fnjs_update_div"]
estado_revision: "generado"
---

# Role Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/role_form.php`

## Vistas Relacionadas

- `frontend/usuarios/view/role_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/role_form.php`
- `frontend/usuarios/controller/role_grupmenu.php`

## Endpoints Usados

- `/src/usuarios/role_grupmenu_del`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_info`

## Capacidades Relacionadas

- `usuarios.role.gestionar`
- `usuarios.role_grupmenu_del.gestionar`
- `usuarios.role_info.gestionar`

## Campos Detectados

- `form.dmz`
- `form.pau`
- `form.que`
- `form.role`
- `form.sel`
- `form.sf`
- `form.sv`
- `html.dmz`
- `html.que`
- `html.role`
- `html.sf`
- `html.sv`
- `post.id_role`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_add_grupmenu`
- `fnjs_del_grupmenu`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
