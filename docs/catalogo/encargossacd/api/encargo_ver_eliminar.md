---
id: "encargossacd.encargo_ver_eliminar"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_ver_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_eliminar.php"
entrada: ["post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoVerEliminarData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_select.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerEliminar"]
tags: ["encargossacd", "encargo", "ver", "eliminar"]
estado_revision: "generado"
---

# Encargo Ver Eliminar

Borrado desde lista `encargo_select` (antes `encargo_ajax.php` que=eliminar).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/encargo_ver_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoVerEliminarData`):
  - `error` (`string`)

## Efectos colaterales

- Borrado desde lista `encargo_select` (antes `encargo_ajax.php` que=eliminar).

## Casos De Uso

- `src\encargossacd\application\EncargoVerEliminar`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.