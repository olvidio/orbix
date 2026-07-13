---
id: "misas.ver_plan_sacd_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_plan_sacd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php"
entrada: ["post.id_sacd:string", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: ["id_sacd"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_VerPlanSacdDataData"
respuesta_data: ["rows:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\VerPlanSacdData"]
tags: ["misas", "ver", "plan", "sacd", "data"]
estado_revision: "revisado"
errores: []
---

# Ver plan sacd Data

Lista cronológica de misas asignadas a un sacerdote en un rango de fechas.

Linaje: Slice 7 — migrado desde apps/misas/controller/ver_plan_sacd.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista cronológica de misas asignadas a un sacerdote en un rango de fechas.

## Endpoint

- URL: `/src/misas/ver_plan_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_sacd` | `string` | application | Si | |
| `periodo` | `string` | application | No | |
| `empiezamin` | `string` | application | No | |
| `empiezamax` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `rows`: array<{dia, encargo, observ}>

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

jefe_zona ve todos los estados comunicados; resto solo STATUS_COMUNICADO_SACD o CTR

## Casos De Uso

- `src\misas\application\VerPlanSacdData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_plan_sacd.php"]`).
