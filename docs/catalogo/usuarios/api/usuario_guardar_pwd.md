---
id: "usuarios.usuario_guardar_pwd"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_guardar_pwd"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php"
entrada: ["post.id_usuario:integer", "post.password:string"]
entrada_obligatoria: []
respuesta: "custom_json"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "guardar", "pwd"]
estado_revision: "generado"
---

# Usuario Guardar Pwd

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_guardar_pwd`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller | No | controller |
| `password` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::send`
- Forma: `custom_json`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_form_pwd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.