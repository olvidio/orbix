---
id: "usuarios.app_session"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/app_session"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/app_session.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "app", "session"]
estado_revision: "revisado"
errores: []
---

# App Session

Comprueba si hay sesión autenticada al arrancar la app móvil (sin credenciales).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Comprueba si hay sesión autenticada al arrancar la app móvil (sin credenciales).

## Endpoint

- URL: `/src/usuarios/app_session`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_session.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `authenticated`: boolean
  - `id_usuario`: int si autenticado
  - `username`: string
  - `esquema`: string

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Lee `$_SESSION['session_auth']`; sin permisos de oficina.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
