---
id: "misas.nuevo_status"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/nuevo_status"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/nuevo_status.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.estado:integer", "post.id_zona:integer", "post.periodo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_NuevoStatusPeriodoData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\NuevoStatusPeriodo"]
tags: ["misas", "nuevo", "status"]
estado_revision: "generado"
---

# Nuevo Status

Actualiza `status` de todos los `EncargoDia` de encargos 8100+ de la zona en el rango.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/nuevo_status`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/nuevo_status.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `estado` | `integer` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_NuevoStatusPeriodoData`):
  - `error` (`string`)

## Casos De Uso

- `src\misas\application\NuevoStatusPeriodo`

## Frontend Relacionado

- `frontend/misas/controller/cambiar_status.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.