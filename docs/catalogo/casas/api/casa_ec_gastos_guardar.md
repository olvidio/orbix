---
id: "casas.casa_ec_gastos_guardar"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ec_gastos_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php"
entrada: ["post.ap_sf$m:string", "post.ap_sv$m:string", "post.g$m:string", "post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_CasaEcGastosGuardarData"
respuesta_data: ["ok:bool, mensaje: string, data: string"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa_ec_gastos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaEcGastosGuardar"]
tags: ["casas", "casa", "ec", "gastos", "guardar"]
estado_revision: "generado"
---

# Casa Ec Gastos Guardar

Endpoint backend: guardar gastos/aportaciones mensuales (`casa_ec_gastos_guardar`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ec_gastos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ap_sf$m` | `string` | controller+application | No | controller+application |
| `ap_sv$m` | `string` | controller+application | No | controller+application |
| `g$m` | `string` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `year` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `casas_CasaEcGastosGuardarData`):
  - `ok` (`bool, mensaje: string, data: string`)

## Casos De Uso

- `src\casas\application\CasaEcGastosGuardar`

## Frontend Relacionado

- `frontend/casas/controller/casa_ec_gastos_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.