---
id: "usuarios.usuario_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_info.php"
entrada: ["post.id_usuario:integer"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_form_mail.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "info"]
estado_revision: "revisado"
errores: ["Id de usuario no válido", "Usuario no encontrado"]
---

# Usuario Info

Resumen usuario para cabecera ficha (grupos, login, email).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resumen usuario para cabecera ficha (grupos, login, email).

## Endpoint

- URL: `/src/usuarios/usuario_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `grupos_txt`: lista grupos
  - `usuario`: login
  - `email`: string

## Errores conocidos
- `Id de usuario no válido`
- `Usuario no encontrado`

## Permisos

Admin en ficha usuario.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_form_mail.php", "frontend/usuarios/controller/usuario_form_pwd.php"]`).
