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
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa_ec_gastos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaEcGastosGuardar"]
tags: ["casas", "casa", "ec", "gastos", "guardar"]
estado_revision: "generado"
---

# Casa Ec Gastos Guardar

Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo. Borra los existentes y los reinserta con fecha 5 de cada mes. Sucesor de la rama `que=guardarGasto` de `apps/casas/controller/casa_ec_ajax.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ec_gastos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ap_sf$m` | `string` | application | No | application |
| `ap_sv$m` | `string` | application | No | application |
| `g$m` | `string` | application | No | application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `year` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\casas\application\CasaEcGastosGuardar`

## Frontend Relacionado

- `frontend/casas/controller/casa_ec_gastos_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.