---
id: "misas.cuadricula_update"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/cuadricula_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/cuadricula_update.php"
entrada: ["post.dia:string", "post.id_enc:integer", "post.id_zona:integer", "post.key:string", "post.observ:string", "post.tend:string", "post.tipo_plantilla:string", "post.tstart:string", "post.uuid_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_CuadriculaUpdateData"
respuesta_data: ["error:string, meta: array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\CuadriculaUpdate"]
tags: ["misas", "cuadricula", "update"]
estado_revision: "generado"
---

# Cuadricula Update

Use case del endpoint `cuadricula_update` (migracion de `apps/misas/controller/cuadricula_update.php` al Slice 6a). Hace dos cosas en la misma transaccion logica: 1. Upsert / delete de un `EncargoDia` para un dia + encargo concretos, en funcion de `key` (si esta vacio, se borra; si trae `id_nom`, se guarda o actualiza). 2. Recalcula el bloque `meta` que la UI usa para pintar colores y textos (disponibilidad del sacd anterior y del nuevo, numero de misas del dia, conflictos con primera hora, etc.). El codigo es una traduccion casi literal del controlador original para minimizar riesgo de regresion: la logica de negocio en si no cambia en este slice; lo unico que cambia es donde vive.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/cuadricula_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/cuadricula_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dia` | `string` | controller | No | controller |
| `id_enc` | `integer` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |
| `key` | `string` | controller | No | controller |
| `observ` | `string` | controller | No | controller |
| `tend` | `string` | controller | No | controller |
| `tipo_plantilla` | `string` | controller | No | controller |
| `tstart` | `string` | controller | No | controller |
| `uuid_item` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_CuadriculaUpdateData`):
  - `error` (`string, meta: array`)

## Casos De Uso

- `src\misas\application\CuadriculaUpdate`

## Frontend Relacionado

- `frontend/misas/support/CuadriculaZonaRenderer.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.