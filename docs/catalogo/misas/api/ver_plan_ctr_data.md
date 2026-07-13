---
id: "misas.ver_plan_ctr_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_plan_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php"
entrada: ["post.id_ubi:integer", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_VerPlanCtrDataData"
respuesta_data: ["columns:array", "rows:array", "legend:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/imprimir_plan_ctr.php", "frontend/misas/controller/ver_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\VerPlanCtrData"]
tags: ["misas", "ver", "plan", "ctr", "data"]
estado_revision: "revisado"
errores: []
---

# Ver plan ctr Data

Genera la cuadrícula del plan de misas por centro: encargos en filas, días en columnas, con leyenda de sacds.

Linaje: Slice 7 — migrado desde apps/misas/controller/ver_plan_ctr.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Genera la cuadrícula del plan de misas por centro: encargos en filas, días en columnas, con leyenda de sacds.

## Endpoint

- URL: `/src/misas/ver_plan_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |
| `periodo` | `string` | application | No | |
| `empiezamin` | `string` | application | No | |
| `empiezamax` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `columns`: array<{letra, num_dia, num_mes, id_dia}>
  - `rows`: array<{desc_enc, cells[]}>
  - `legend`: array<{iniciales, nombre}>

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Visibilidad celdas por rol: jefe_zona, ctr (STATUS_COMUNICADO_CTR), sacd (COMUNICADO_SACD|CTR)

## Casos De Uso

- `src\misas\application\VerPlanCtrData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/imprimir_plan_ctr.php", "frontend/misas/controller/ver_plan_ctr.php"]`).
