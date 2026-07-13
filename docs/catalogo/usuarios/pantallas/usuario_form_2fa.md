---
id: "usuarios.pantalla.usuario_form_2fa"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Form 2fa"
controller: "frontend/usuarios/controller/usuario_form_2fa.php"
vistas: ["frontend/usuarios/view/usuario_form_2fa.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/ayuda_2fa_reset.php", "frontend/usuarios/controller/usuario_reset_2fa.php"]
endpoints: ["/src/usuarios/usuario_2fa_info", "/src/usuarios/usuario_2fa_update", "/src/usuarios/usuario_2fa_verify", "/src/usuarios/usuario_info"]
capacidades: ["usuarios.usuario_2fa.gestionar", "usuarios.usuario_2fa_info.gestionar", "usuarios.usuario_2fa_verify.gestionar", "usuarios.usuario_info.gestionar"]
campos: ["form.enable_2fa", "form.secret_2fa", "form.verification_code", "html.btn_ok", "html.enable_2fa", "html.id_usuario", "html.verification_code"]
acciones: ["fnjs_enviar", "fnjs_guardar", "fnjs_guardar_datos", "fnjs_logout"]
estado_revision: "revisado"
---

# Usuario Form 2fa

Configuración 2FA: activar/desactivar TOTP con verificación previa.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/usuarios/controller/usuario_form_2fa.php`

## Vistas Relacionadas

- `frontend/usuarios/view/usuario_form_2fa.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/ayuda_2fa_reset.php`
- `frontend/usuarios/controller/usuario_reset_2fa.php`

## Endpoints Usados

- `/src/usuarios/usuario_2fa_info`
- `/src/usuarios/usuario_2fa_update`
- `/src/usuarios/usuario_2fa_verify`
- `/src/usuarios/usuario_info`

## Capacidades Relacionadas

- `usuarios.usuario_2fa.gestionar`
- `usuarios.usuario_2fa_info.gestionar`
- `usuarios.usuario_2fa_verify.gestionar`
- `usuarios.usuario_info.gestionar`

## Campos Detectados

- `form.enable_2fa`
- `form.secret_2fa`
- `form.verification_code`
- `html.btn_ok`
- `html.enable_2fa`
- `html.id_usuario`
- `html.verification_code`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
