---
id: "usuarios.usuario_2fa_update"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_2fa_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php"
entrada: ["post.id_usuario:integer", "post.secret_2fa:string", "post.enable_2fa:boolean", "post.verification_code:string"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_reset_2fa.php"]
casos_uso: []
tags: ["usuarios", "usuario", "2fa", "update"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "Se requiere un código de verificación para activar 2FA", "Código de verificación inválido", "Hay un error, no se ha guardado"]
---

# Usuario 2fa Update

Activa/desactiva 2FA verificando TOTP al activar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Activa/desactiva 2FA verificando TOTP al activar.

## Endpoint

- URL: `/src/usuarios/usuario_2fa_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |
| `secret_2fa` | `string` | application | No | |
| `enable_2fa` | `boolean` | application | No | |
| `verification_code` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `success`: true

## Errores conocidos
- `Usuario no encontrado`
- `Se requiere un código de verificación para activar 2FA`
- `Código de verificación inválido`
- `Hay un error, no se ha guardado`

## Permisos

Usuario autenticado en formulario 2FA.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_reset_2fa.php"]`).
