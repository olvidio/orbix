---
id: "casas.casa_ingreso_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php"
entrada: ["post.id_activ:integer", "post.id_tarifa:string", "post.ingresos:string", "post.num_asistentes:integer", "post.observ:string", "post.precio:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_CasaIngresoUpdateData"
respuesta_data: ["ok:bool, mensaje: string, data: string"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoUpdate"]
tags: ["casas", "casa", "ingreso", "update"]
estado_revision: "generado"
---

# Casa Ingreso Update

Endpoint backend: crear/actualizar ingreso y tarifa de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ingreso_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_tarifa` | `string` | controller+application | No | controller+application |
| `ingresos` | `string` | controller+application | No | controller+application |
| `num_asistentes` | `integer` | controller+application | No | controller+application |
| `observ` | `string` | controller+application | No | controller+application |
| `precio` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `casas_CasaIngresoUpdateData`):
  - `ok` (`bool, mensaje: string, data: string`)

## Casos De Uso

- `src\casas\application\CasaIngresoUpdate`

## Frontend Relacionado

- `frontend/casas/controller/casa.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.