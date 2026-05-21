---
id: "usuarios.usuario_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_guardar.php"
entrada: ["post.cambio_password:boolean", "post.casas:array", "post.ctx:string", "post.email:string", "post.has_2fa:boolean", "post.id_ctr:integer", "post.id_nom:integer", "post.id_role:integer", "post.nom_usuario:string", "post.pass:string", "post.password:string", "post.perm_activ:array", "post.usuario:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "guardar"]
estado_revision: "generado"
---

# Usuario Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cambio_password` | `boolean` | controller | No | controller |
| `casas` | `array` | controller | No | controller |
| `ctx` | `string` | controller | No | controller |
| `email` | `string` | controller | No | controller |
| `has_2fa` | `boolean` | controller | No | controller |
| `id_ctr` | `integer` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |
| `id_role` | `integer` | controller | No | controller |
| `nom_usuario` | `string` | controller | No | controller |
| `pass` | `string` | controller | No | controller |
| `password` | `string` | controller | No | controller |
| `perm_activ` | `array` | controller | No | controller |
| `usuario` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_form.php`
- `frontend/usuarios/controller/usuario_form_pwd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.