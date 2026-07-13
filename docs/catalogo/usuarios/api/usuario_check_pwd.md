---
id: "usuarios.usuario_check_pwd"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_check_pwd"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php"
entrada: ["post.id_usuario:integer", "post.usuario:string", "post.password:string"]
entrada_obligatoria: ["password"]
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "check", "pwd"]
estado_revision: "revisado"
errores: []
---

# Usuario Check Pwd

Valida fortaleza de contraseña (JsonResponse directo, no envelope ContestarJson).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida fortaleza de contraseña (JsonResponse directo, no envelope ContestarJson).

## Endpoint

- URL: `/src/usuarios/usuario_check_pwd`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | No | |
| `usuario` | `string` | application | No | |
| `password` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `success`: boolean
  - `mensaje`: validación PasswordHasher

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Formularios pwd usuario (nuevo/edición).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php"]`).
