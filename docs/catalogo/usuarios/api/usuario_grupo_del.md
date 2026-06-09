---
id: "usuarios.usuario_grupo_del"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_grupo_del"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del.php"
entrada: ["post.ctx:string", "post.id_grupo:integer", "post.id_usuario:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_grupo_del_lst.php", "frontend/usuarios/view/usuario_grupo.phtml"]
casos_uso: []
tags: ["usuarios", "usuario", "grupo", "del"]
estado_revision: "generado"
---

# Usuario Grupo Del

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_grupo_del`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | controller | No | controller |
| `id_grupo` | `integer` | controller | No | controller |
| `id_usuario` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_grupo_del_lst.php`
- `frontend/usuarios/view/usuario_grupo.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.