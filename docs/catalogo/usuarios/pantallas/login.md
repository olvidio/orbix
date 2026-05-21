---
id: "usuarios.pantalla.login"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "usuarios"
nombre: "Login"
controller: "frontend/usuarios/controller/login.php"
vistas: ["frontend/usuarios/view/login_form.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/ayuda_2fa_reset.php", "frontend/usuarios/controller/ayuda_acceso.php"]
endpoints: []
capacidades: []
campos: ["html.esquema", "html.idioma", "html.password", "html.username", "html.verification_code"]
acciones: ["fnjs_goHelp", "fnjs_goTop", "fnjs_hideErrors"]
estado_revision: "generado"
---

# Login

Guardia de sesion del sistema web.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/usuarios/controller/login.php`

## Vistas Relacionadas

- `frontend/usuarios/view/login_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/ayuda_2fa_reset.php`
- `frontend/usuarios/controller/ayuda_acceso.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `html.esquema`
- `html.idioma`
- `html.password`
- `html.username`
- `html.verification_code`

## Acciones Detectadas

- `fnjs_goHelp`
- `fnjs_goTop`
- `fnjs_hideErrors`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
