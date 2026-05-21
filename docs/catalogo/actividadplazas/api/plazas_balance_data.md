---
id: "actividadplazas.plazas_balance_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/plazas_balance_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_data.php"
entrada: ["post.dl:string", "post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PlazasBalanceDataData"
respuesta_data: ["dlA:string", "dlB:string", "concedidasA2B:integer", "concedidasB2A:integer", "a_cabeceras:array", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/plazas_balance_dl.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasBalanceData"]
tags: ["actividadplazas", "plazas", "balance", "data"]
estado_revision: "generado"
---

# Plazas Balance Data

Endpoint backend: datos del grid comparativo A vs B (plazas concedidas y libres entre dos dl para un tipo de actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/plazas_balance_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_PlazasBalanceDataData`):
  - `dlA` (`string`)
  - `dlB` (`string`)
  - `concedidasA2B` (`integer`)
  - `concedidasB2A` (`integer`)
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)

## Casos De Uso

- `src\actividadplazas\application\PlazasBalanceData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/plazas_balance_dl.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.