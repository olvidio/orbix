---
id: "usuarios.usuario_check_pwd"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_check_pwd"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php"
entrada: ["post.id_usuario:integer", "post.password:string", "post.usuario:string"]
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
casos_uso: []
tags: ["usuarios", "usuario", "check", "pwd"]
estado_revision: "generado"
---

# Usuario Check Pwd

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_check_pwd`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller | No | controller |
| `password` | `string` | controller | No | controller |
| `usuario` | `string` | controller | No | controller |

## Salida

No se ha detectado salida estandar. Revisar manualmente.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_form.php`
- `frontend/usuarios/controller/usuario_form_pwd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.