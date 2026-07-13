---
id: "usuarios.usuario_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_guardar.php"
entrada: ["post.ctx:string", "post.usuario:string", "post.id_role:integer", "post.email:string", "post.nom_usuario:string", "post.password:string", "post.id_nom:integer", "post.id_ctr:integer", "post.casas:array", "post.cambio_password:boolean", "post.has_2fa:boolean", "post.perm_activ:array"]
entrada_obligatoria: ["ctx", "usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "guardar"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "debe poner un nombre", "Usuario no encontrado", "hay un error, no se ha guardado"]
---

# Usuario Guardar

Alta/edición usuario con rol, pau, casas/centro, flags pwd/2FA y permisos actividad embebidos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta/edición usuario con rol, pau, casas/centro, flags pwd/2FA y permisos actividad embebidos.

## Endpoint

- URL: `/src/usuarios/usuario_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | application | Si | |
| `usuario` | `string` | application | Si | |
| `id_role` | `integer` | application | No | |
| `email` | `string` | application | No | |
| `nom_usuario` | `string` | application | No | |
| `password` | `string` | application | No | |
| `id_nom` | `integer` | application | No | |
| `id_ctr` | `integer` | application | No | |
| `casas` | `array` | application | No | |
| `cambio_password` | `boolean` | application | No | |
| `has_2fa` | `boolean` | application | No | |
| `perm_activ` | `array` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Operación no autorizada`
- `debe poner un nombre`
- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Permisos

Admin; ctx HashB `usuario_guardar` coherente con que_user/id_usuario.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php"]`).
