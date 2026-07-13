---
id: "usuarios.recuperar_password_mail"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/recuperar_password_mail"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php"
entrada: ["post.username:string", "post.ubicacion:string", "post.esquema:string", "post.esquema_web:string", "post.url_index:string"]
entrada_obligatoria: ["username"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "recuperar", "password", "mail"]
estado_revision: "revisado"
errores: ["Esquema no válido", "Error al preparar la consulta", "Error al ejecutar la consulta", "No hay email asociado a este usuario", "Error al enviar el correo electrónico", "Error al actualizar la contraseña", "No se encontró ningún usuario con ese nombre"]
---

# Recuperar Password Mail

Recuperación contraseña: genera pwd temporal, marca cambio obligatorio y envía mail.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recuperación contraseña: genera pwd temporal, marca cambio obligatorio y envía mail.

## Endpoint

- URL: `/src/usuarios/recuperar_password_mail`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | application | Si | |
| `ubicacion` | `string` | application | No | |
| `esquema` | `string` | application | No | |
| `esquema_web` | `string` | application | No | |
| `url_index` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `success`: boolean
  - `email`: string

## Errores conocidos
- `Esquema no válido`
- `Error al preparar la consulta`
- `Error al ejecutar la consulta`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `Error al actualizar la contraseña`
- `No se encontró ningún usuario con ese nombre`

## Permisos

Público (pantalla recuperar_password).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
