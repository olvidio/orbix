---
id: "cartaspresentacion.poblaciones_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/poblaciones_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/poblaciones_data.php"
entrada: ["post.filtro:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionPoblacionesDataData"
respuesta_data: ["id:string", "opciones:array", "selected:string", "blanco:boolean", "val_blanco:string", "action:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionPoblacionesData"]
tags: ["cartaspresentacion", "poblaciones", "data"]
estado_revision: "generado"
---

# Poblaciones Data

Endpoint backend: opciones del desplegable de poblaciones segun el filtro elegido (`get_H`, `get_r`, `get_dl`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/poblaciones_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/poblaciones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cartaspresentacion_CartasPresentacionPoblacionesDataData`):
  - `id` (`string`)
  - `opciones` (`array`)
  - `selected` (`string`)
  - `blanco` (`boolean`)
  - `val_blanco` (`string`)
  - `action` (`string`)

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionPoblacionesData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.