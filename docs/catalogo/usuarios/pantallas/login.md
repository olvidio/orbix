---
id: "usuarios.pantalla.login"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "usuarios"
nombre: "Login"
controller: "frontend/usuarios/controller/login.php"
vistas: ["frontend/usuarios/view/login_form.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/ayuda_2fa_reset.php", "frontend/usuarios/controller/ayuda_acceso.php"]
endpoints: []
capacidades: []
campos: ["html.esquema", "html.idioma", "html.password", "html.username", "html.verification_code"]
acciones: ["fnjs_goHelp", "fnjs_goTop", "fnjs_hideErrors"]
estado_revision: "revisado"
---

# Login

Pantalla login web (HTML, no JSON).

## Tipo

- Subtipo: `pantalla_principal`


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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
