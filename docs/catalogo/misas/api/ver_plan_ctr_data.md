---
id: "misas.ver_plan_ctr_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_plan_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_ubi:integer", "post.periodo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_VerPlanCtrDataData"
respuesta_data: ["columns:array", "rows:array", "legend:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/imprimir_plan_ctr.php", "frontend/misas/controller/ver_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\VerPlanCtrData"]
tags: ["misas", "ver", "plan", "ctr", "data"]
estado_revision: "generado"
---

# Ver Plan Ctr Data

Datos para la vista `ver_plan_ctr.phtml`: cuadricula del plan de misas por centro (filas: encargos, columnas: días).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/ver_plan_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_VerPlanCtrDataData`):
  - `columns` (`array`)
  - `rows` (`array`)
  - `legend` (`array`)

## Casos De Uso

- `src\misas\application\VerPlanCtrData`

## Frontend Relacionado

- `frontend/misas/controller/imprimir_plan_ctr.php`
- `frontend/misas/controller/ver_plan_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.