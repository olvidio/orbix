---
id: "usuarios.usuario_guardar_mail"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_guardar_mail"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_mail.php"
entrada: ["post.id_usuario:integer", "post.email:string"]
entrada_obligatoria: ["id_usuario", "email"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "usuario", "guardar", "mail"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "hay un error, no se ha guardado"]
---

# Usuario Guardar Mail

Actualiza email del usuario (preferencias o admin).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza email del usuario (preferencias o admin).

## Endpoint

- URL: `/src/usuarios/usuario_guardar_mail`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_mail.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |
| `email` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Permisos

Usuario propio o admin.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
