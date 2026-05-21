---
id: "usuarios.usuario_eliminar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_lista.php"]
casos_uso: ["src\\usuarios\\application\\usuarioEliminar"]
tags: ["usuarios", "usuario", "eliminar"]
estado_revision: "generado"
---

# Usuario Eliminar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller | No | controller |

## Salida

No se ha detectado salida estandar. Revisar manualmente.

## Casos De Uso

- `src\usuarios\application\usuarioEliminar`

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.