---
id: "cambios.cambio_usuario_eliminar_hasta_fecha"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_eliminar_hasta_fecha"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar_hasta_fecha.php"
entrada: ["post.f_fin:string"]
entrada_obligatoria: ["f_fin"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/avisos_generar.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioEliminarHastaFecha"]
tags: ["cambios", "cambio", "usuario", "eliminar", "hasta", "fecha"]
estado_revision: "revisado"
---

# Cambio Usuario Eliminar Hasta Fecha

Elimina los `CambioUsuario` con fecha anterior o igual a `f_fin`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Purgado masivo de cambios anotados hasta una fecha límite. Requiere `f_fin` no vacío.

## Endpoint

- URL: `/src/cambios/cambio_usuario_eliminar_hasta_fecha`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar_hasta_fecha.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_fin` | `string` | controller+application | Sí | Fecha límite (formato del formulario) |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: "ok"`.
- Error: mensaje en el envelope.

## Errores conocidos

- `debe indicar la fecha`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

## Permisos

- Sin control propio; autorización en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioEliminarHastaFecha`

## Frontend Relacionado

- `frontend/cambios/controller/avisos_generar.php`: `fnjs_borrar_hasta_fecha` envía `f_fin` firmado
  vía `hash_eliminar_fecha`.
