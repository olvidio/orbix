---
id: "usuarios.check_first_login_2fa"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/check_first_login_2fa"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/check_first_login_2fa.php"
entrada: []
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "check", "first", "login", "2fa"]
estado_revision: "revisado"
errores: []
---

# Check First Login 2fa

Tras login web, redirige a configuración 2FA si el usuario no la tiene activada; si no, continúa al home.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tras login web, redirige a configuración 2FA si el usuario no la tiene activada; si no, continúa al home.

## Endpoint

- URL: `/src/usuarios/check_first_login_2fa`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/check_first_login_2fa.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: redirección HTTP (no JSON); flujo post-login 2FA.

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Usuario autenticado (`ConfigGlobal::MiUsuario()`).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
