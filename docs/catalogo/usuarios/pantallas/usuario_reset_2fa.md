---
id: "usuarios.pantalla.usuario_reset_2fa"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Reset 2fa"
controller: "frontend/usuarios/controller/usuario_reset_2fa.php"
vistas: []
fragmentos_frontend: ["frontend/usuarios/controller/usuario_form_2fa.php"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
capacidades: ["usuarios.usuario_2fa.gestionar"]
campos: ["post.id_usuario"]
acciones: []
estado_revision: "generado"
---

# Usuario Reset 2fa

Controlador para restablecer la configuración de autenticación de dos factores (2FA).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/usuario_reset_2fa.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/usuario_form_2fa.php`

## Endpoints Usados

- `/src/usuarios/usuario_2fa_update`

## Capacidades Relacionadas

- `usuarios.usuario_2fa.gestionar`

## Campos Detectados

- `post.id_usuario`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
