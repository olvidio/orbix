---
id: "usuarios.usuario_form"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_form"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_form.php"
entrada: ["post.id_usuario:integer", "post.quien:string"]
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php"]
casos_uso: []
tags: ["usuarios", "usuario", "form"]
estado_revision: "generado"
---

# Usuario Form

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_form`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_form.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller | No | controller |
| `quien` | `string` | controller | No | controller |

## Salida

No se ha detectado salida estandar. Revisar manualmente.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.