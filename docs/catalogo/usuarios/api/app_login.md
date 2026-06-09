---
id: "usuarios.app_login"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/app_login"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/app_login.php"
entrada: ["post.esquema:string", "post.password:string", "post.username:string", "post.verification_code:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_AppMobileLoginData"
respuesta_data: ["ok:bool, code?: string, mensaje?: string, data?: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\usuarios\\application\\AppMobileLogin"]
tags: ["usuarios", "app", "login"]
estado_revision: "generado"
---

# App Login

Login JSON para app móvil (Camino B).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/app_login`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_login.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema` | `string` | controller+application | No | controller+application |
| `password` | `string` | controller+application | No | controller+application |
| `username` | `string` | controller+application | No | controller+application |
| `verification_code` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `usuarios_AppMobileLoginData`):
  - `ok` (`bool, code?: string, mensaje?: string, data?: array<string, mixed>`)

## Casos De Uso

- `src\usuarios\application\AppMobileLogin`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.