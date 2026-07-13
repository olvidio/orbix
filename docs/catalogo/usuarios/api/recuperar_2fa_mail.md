---
id: "usuarios.recuperar_2fa_mail"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/recuperar_2fa_mail"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/recuperar_2fa_mail.php"
entrada: ["post.username:string", "post.ubicacion:string", "post.esquema:string", "post.esquema_web:string", "post.url_base:string"]
entrada_obligatoria: ["username"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "recuperar", "2fa", "mail"]
estado_revision: "revisado"
errores: ["Esquema no válido", "No hay email asociado a este usuario", "Error al enviar el correo electrónico", "No se encontró ningún usuario con ese nombre"]
---

# Recuperar 2fa Mail

Recuperación 2FA: genera código/link y envía mail al usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recuperación 2FA: genera código/link y envía mail al usuario.

## Endpoint

- URL: `/src/usuarios/recuperar_2fa_mail`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_2fa_mail.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | application | Si | |
| `ubicacion` | `string` | application | No | |
| `esquema` | `string` | application | No | |
| `esquema_web` | `string` | application | No | |
| `url_base` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `success`: boolean
  - `email`: email ofuscado o ????

## Errores conocidos
- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

## Permisos

Público (pantalla login/recuperar_2fa).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
