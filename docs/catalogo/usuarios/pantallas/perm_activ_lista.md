---
id: "usuarios.pantalla.perm_activ_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Perm Activ Lista"
controller: "frontend/usuarios/controller/perm_activ_lista.php"
vistas: ["frontend/usuarios/view/perm_activ_lista.phtml"]
fragmentos_frontend: ["frontend/procesos/controller/usuario_perm_activ.php"]
endpoints: ["/src/usuarios/perm_activ_lista"]
capacidades: ["usuarios.perm_activ.gestionar"]
campos: ["form.que", "form.sel", "html.que", "post.id_usuario", "post.olvidar", "post.quien"]
acciones: ["fnjs_add_perm_activ", "fnjs_del_perm_activ", "fnjs_enviar_formulario", "fnjs_mod_perm_activ", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Perm Activ Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/perm_activ_lista.php`

## Vistas Relacionadas

- `frontend/usuarios/view/perm_activ_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/procesos/controller/usuario_perm_activ.php`

## Endpoints Usados

- `/src/usuarios/perm_activ_lista`

## Capacidades Relacionadas

- `usuarios.perm_activ.gestionar`

## Campos Detectados

- `form.que`
- `form.sel`
- `html.que`
- `post.id_usuario`
- `post.olvidar`
- `post.quien`

## Acciones Detectadas

- `fnjs_add_perm_activ`
- `fnjs_del_perm_activ`
- `fnjs_enviar_formulario`
- `fnjs_mod_perm_activ`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
