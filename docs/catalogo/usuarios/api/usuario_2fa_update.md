---
id: "usuarios.usuario_2fa_update"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_2fa_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php"
entrada: ["post.enable_2fa:boolean", "post.id_usuario:integer", "post.secret_2fa:string", "post.verification_code:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_reset_2fa.php"]
casos_uso: []
tags: ["usuarios", "usuario", "2fa", "update"]
estado_revision: "generado"
---

# Usuario 2fa Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_2fa_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `enable_2fa` | `boolean` | controller | No | controller |
| `id_usuario` | `integer` | controller | No | controller |
| `secret_2fa` | `string` | controller | No | controller |
| `verification_code` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_form_2fa.php`
- `frontend/usuarios/controller/usuario_reset_2fa.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.