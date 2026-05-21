---
id: "cambios.pantalla.usuario_form_avisos"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Usuario Form Avisos"
controller: "frontend/cambios/controller/usuario_form_avisos.php"
vistas: ["frontend/cambios/view/usuario_form_avisos.phtml"]
fragmentos_frontend: ["frontend/cambios/controller/usuario_avisos_pref.php"]
endpoints: ["/src/cambios/usuario_form_avisos_data"]
capacidades: ["cambios.usuario_form_avisos.gestionar"]
campos: ["post.id_usuario", "post.quien"]
acciones: ["fnjs_add_cambio", "fnjs_del_cambio", "fnjs_enviar_formulario", "fnjs_mod_cambio", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Usuario Form Avisos

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_form_avisos.php`

## Vistas Relacionadas

- `frontend/cambios/view/usuario_form_avisos.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cambios/controller/usuario_avisos_pref.php`

## Endpoints Usados

- `/src/cambios/usuario_form_avisos_data`

## Capacidades Relacionadas

- `cambios.usuario_form_avisos.gestionar`

## Campos Detectados

- `post.id_usuario`
- `post.quien`

## Acciones Detectadas

- `fnjs_add_cambio`
- `fnjs_del_cambio`
- `fnjs_enviar_formulario`
- `fnjs_mod_cambio`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
