---
id: "misas.ver_plan_sacd_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_plan_sacd_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_sacd:string", "post.periodo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_VerPlanSacdDataData"
respuesta_data: ["rows:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\VerPlanSacdData"]
tags: ["misas", "ver", "plan", "sacd", "data"]
estado_revision: "generado"
---

# Ver Plan Sacd Data

Datos para la vista `ver_plan_sacd.phtml`: plan de misas de un sacerdote en un rango de fechas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/ver_plan_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_sacd` | `string` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_VerPlanSacdDataData`):
  - `rows` (`array`)

## Casos De Uso

- `src\misas\application\VerPlanSacdData`

## Frontend Relacionado

- `frontend/misas/controller/ver_plan_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.