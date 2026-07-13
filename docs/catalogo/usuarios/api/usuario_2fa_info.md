---
id: "usuarios.usuario_2fa_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_2fa_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_info.php"
entrada: ["post.id_usuario:integer"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form_2fa.php"]
casos_uso: []
tags: ["usuarios", "usuario", "2fa", "info"]
estado_revision: "revisado"
errores: ["Id de usuario no válido", "Usuario no encontrado"]
---

# Usuario 2fa Info

Estado 2FA del usuario para formulario configuración.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Estado 2FA del usuario para formulario configuración.

## Endpoint

- URL: `/src/usuarios/usuario_2fa_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `has_2fa`: boolean
  - `secret_2fa`: string|null

## Errores conocidos
- `Id de usuario no válido`
- `Usuario no encontrado`

## Permisos

Usuario propio o admin en `usuario_form_2fa`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form_2fa.php"]`).
