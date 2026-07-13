---
id: "usuarios.usuario_2fa_verify"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_2fa_verify"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_verify.php"
entrada: ["post.verification_code:string", "post.secret_2fa:string"]
entrada_obligatoria: ["verification_code", "secret_2fa"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form_2fa.php"]
casos_uso: []
tags: ["usuarios", "usuario", "2fa", "verify"]
estado_revision: "revisado"
errores: ["Código de verificación o clave secreta no válidos", "Código de verificación inválido"]
---

# Usuario 2fa Verify

Valida código TOTP contra secret provisional (paso previo a activar 2FA).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida código TOTP contra secret provisional (paso previo a activar 2FA).

## Endpoint

- URL: `/src/usuarios/usuario_2fa_verify`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_verify.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `verification_code` | `string` | application | Si | |
| `secret_2fa` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `valid`: boolean

## Errores conocidos
- `Código de verificación o clave secreta no válidos`
- `Código de verificación inválido`

## Permisos

Usuario en wizard 2FA.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form_2fa.php"]`).
