---
id: "usuarios.pantalla.role_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Role Lista"
controller: "frontend/usuarios/controller/role_lista.php"
vistas: ["frontend/usuarios/view/role_lista.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/role_form.php", "frontend/usuarios/controller/role_lista.php"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_lista"]
capacidades: ["usuarios.role.gestionar"]
campos: ["form.sel", "post.id_sel", "post.scroll_id", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "generado"
---

# Role Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
