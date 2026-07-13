---
id: "usuarios.app_login"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/app_login"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/app_login.php"
entrada: ["post.username:string", "post.password:string", "post.esquema:string", "post.verification_code:string"]
entrada_obligatoria: ["username", "password"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_AppMobileLoginData"
respuesta_data: ["ok:bool, code?: string, mensaje?: string, data?: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\usuarios\\application\\AppMobileLogin"]
tags: ["usuarios", "app", "login"]
estado_revision: "revisado"
errores: ["Usuario y contraseña obligatorios", "Esquema no indicado", "Esquema no válido", "Error de autenticación"]
---

# App Login

Login JSON para app móvil: valida credenciales (y 2FA si aplica), establece sesión PHP y devuelve payload con códigos de estado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Login JSON para app móvil: valida credenciales (y 2FA si aplica), establece sesión PHP y devuelve payload con códigos de estado.

## Endpoint

- URL: `/src/usuarios/app_login`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_login.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | application | Si | |
| `password` | `string` | application | Si | |
| `esquema` | `string` | application | No | |
| `verification_code` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `code`: código app (need_2fa, ok, …)
  - `authenticated`: boolean tras éxito

## Errores conocidos
- `Usuario y contraseña obligatorios`
- `Esquema no indicado`
- `Esquema no válido`
- `Error de autenticación`

## Permisos

Público (sin sesión previa).

## Casos De Uso

- `src\usuarios\application\AppMobileLogin`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
