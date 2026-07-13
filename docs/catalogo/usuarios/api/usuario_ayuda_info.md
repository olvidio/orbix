---
id: "usuarios.usuario_ayuda_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_ayuda_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_ayuda_info.php"
entrada: ["post.username:string", "post.ubicacion:string", "post.esquema:string", "post.esquema_web:string"]
entrada_obligatoria: ["username"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "usuario", "ayuda", "info"]
estado_revision: "revisado"
errores: ["Esquema no válido", "Debe ingresar un nombre de usuario válido", "No hay email asociado a este usuario"]
---

# Usuario Ayuda Info

Ayuda acceso login: email ofuscado del usuario y contacto admin regional.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Ayuda acceso login: email ofuscado del usuario y contacto admin regional.

## Endpoint

- URL: `/src/usuarios/usuario_ayuda_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_ayuda_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | application | Si | |
| `ubicacion` | `string` | application | No | |
| `esquema` | `string` | application | No | |
| `esquema_web` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `emailOfuscado`: string
  - `mail_admin`: email admin circunscripción
  - `errores`: string

## Errores conocidos
- `Esquema no válido`
- `Debe ingresar un nombre de usuario válido`
- `No hay email asociado a este usuario`

## Permisos

Público (pantalla ayuda_acceso).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
