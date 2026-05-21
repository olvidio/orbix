---
id: "usuarios.pantalla.usuario_form_mail"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Form Mail"
controller: "frontend/usuarios/controller/usuario_form_mail.php"
vistas: ["frontend/usuarios/view/usuario_form_mail.phtml"]
fragmentos_frontend: []
endpoints: ["/src/usuarios/usuario_info"]
capacidades: ["usuarios.usuario_info.gestionar"]
campos: ["form.email"]
acciones: ["fnjs_guardar"]
estado_revision: "generado"
---

# Usuario Form Mail

Formulario para cambiar el mail por parte del usuario.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/usuario_form_mail.php`

## Vistas Relacionadas

- `frontend/usuarios/view/usuario_form_mail.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/usuarios/usuario_info`

## Capacidades Relacionadas

- `usuarios.usuario_info.gestionar`

## Campos Detectados

- `form.email`

## Acciones Detectadas

- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
