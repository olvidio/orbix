---
id: "usuarios.usuario_guardar_pwd"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_guardar_pwd"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php"
entrada: ["post.id_usuario:integer", "post.password:string"]
entrada_obligatoria: ["id_usuario", "password"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "guardar", "pwd"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "hay un error, no se ha guardado"]
---

# Usuario Guardar Pwd

Cambia contraseña tras validar fortaleza; limpia flag cambio_password.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Cambia contraseña tras validar fortaleza; limpia flag cambio_password.

## Endpoint

- URL: `/src/usuarios/usuario_guardar_pwd`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |
| `password` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Permisos

Usuario autenticado en `usuario_form_pwd`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form_pwd.php"]`).
