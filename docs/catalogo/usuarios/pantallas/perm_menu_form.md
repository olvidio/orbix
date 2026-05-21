---
id: "usuarios.pantalla.perm_menu_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Perm Menu Form"
controller: "frontend/usuarios/controller/perm_menu_form.php"
vistas: ["frontend/usuarios/view/perm_menu_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_info"]
capacidades: ["usuarios.perm_menu.gestionar", "usuarios.perm_menu_info.gestionar"]
campos: ["form.menu_perm", "post.id_item", "post.id_usuario", "post.sel"]
acciones: ["fnjs_grabar"]
estado_revision: "generado"
---

# Perm Menu Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/perm_menu_form.php`

## Vistas Relacionadas

- `frontend/usuarios/view/perm_menu_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_info`

## Capacidades Relacionadas

- `usuarios.perm_menu.gestionar`
- `usuarios.perm_menu_info.gestionar`

## Campos Detectados

- `form.menu_perm`
- `post.id_item`
- `post.id_usuario`
- `post.sel`

## Acciones Detectadas

- `fnjs_grabar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
