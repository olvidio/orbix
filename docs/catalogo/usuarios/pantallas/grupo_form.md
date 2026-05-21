---
id: "usuarios.pantalla.grupo_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Grupo Form"
controller: "frontend/usuarios/controller/grupo_form.php"
vistas: ["frontend/usuarios/view/grupo_form.phtml", "frontend/usuarios/view/perm_menu_lista.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/grupo_form.php", "frontend/usuarios/controller/perm_activ_lista.php", "frontend/usuarios/controller/perm_menu_form.php"]
endpoints: ["/src/usuarios/grupo_info", "/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_lista"]
capacidades: ["usuarios.grupo_info.gestionar", "usuarios.perm_menu.gestionar"]
campos: ["form.que", "form.sel", "form.usuario", "html.que", "html.refresh", "post.id_usuario", "post.que", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_add_perm_menu", "fnjs_del_perm_menu", "fnjs_enviar_formulario", "fnjs_guardar", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Grupo Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/grupo_form.php`

## Vistas Relacionadas

- `frontend/usuarios/view/grupo_form.phtml`
- `frontend/usuarios/view/perm_menu_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/grupo_form.php`
- `frontend/usuarios/controller/perm_activ_lista.php`
- `frontend/usuarios/controller/perm_menu_form.php`

## Endpoints Usados

- `/src/usuarios/grupo_info`
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_lista`

## Capacidades Relacionadas

- `usuarios.grupo_info.gestionar`
- `usuarios.perm_menu.gestionar`

## Campos Detectados

- `form.que`
- `form.sel`
- `form.usuario`
- `html.que`
- `html.refresh`
- `post.id_usuario`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_add_perm_menu`
- `fnjs_del_perm_menu`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
