---
id: "cambios.cambio_usuario_eliminar_hasta_fecha"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_eliminar_hasta_fecha"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar_hasta_fecha.php"
entrada: ["post.f_fin:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_CambioUsuarioEliminarHastaFechaData"
respuesta_data: ["ok:bool, mensaje: string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioEliminarHastaFecha"]
tags: ["cambios", "cambio", "usuario", "eliminar", "hasta", "fecha"]
estado_revision: "generado"
---

# Cambio Usuario Eliminar Hasta Fecha

Endpoint backend: elimina los `CambioUsuario` con fecha <= `f_fin`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_eliminar_hasta_fecha`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar_hasta_fecha.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_fin` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_CambioUsuarioEliminarHastaFechaData`):
  - `ok` (`bool, mensaje: string`)

## Efectos colaterales

- Caso de uso: elimina los `CambioUsuario` con fecha anterior o igual a la indicada.
- Sucesor de la rama `que=eliminar_fecha` del dispatcher `apps/cambios/controller/avisos_generar_ajax.php`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioEliminarHastaFecha`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.