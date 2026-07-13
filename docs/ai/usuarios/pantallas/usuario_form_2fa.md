---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Usuario Form 2fa"
pantalla: "usuarios.pantalla.usuario_form_2fa"
preguntas: ["Que se puede hacer en Usuario Form 2fa?", "Que campos tiene Usuario Form 2fa?", "Que acciones hay en Usuario Form 2fa?"]
capacidades: ["usuarios.usuario_2fa.gestionar", "usuarios.usuario_2fa_info.gestionar", "usuarios.usuario_2fa_verify.gestionar", "usuarios.usuario_info.gestionar"]
endpoints: ["/src/usuarios/usuario_2fa_info", "/src/usuarios/usuario_2fa_update", "/src/usuarios/usuario_2fa_verify", "/src/usuarios/usuario_info"]
source: "docs/catalogo/usuarios/pantallas/usuario_form_2fa.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Usuario Form 2fa

## Resumen

Configuración 2FA: activar/desactivar TOTP con verificación previa.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `usuarios.usuario_2fa.gestionar`
- `usuarios.usuario_2fa_info.gestionar`
- `usuarios.usuario_2fa_verify.gestionar`
- `usuarios.usuario_info.gestionar`

## Endpoints Relacionados

- `/src/usuarios/usuario_2fa_info`
- `/src/usuarios/usuario_2fa_update`
- `/src/usuarios/usuario_2fa_verify`
- `/src/usuarios/usuario_info`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
