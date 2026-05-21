---
id: "misas.buscar_plan_ctr_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/buscar_plan_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_BuscarPlanCtrDataData"
respuesta_data: ["view:'sacd'|'centro'|'none'", "zonas_opciones:array", "zonas_selected:integer", "centros_opciones:array", "centros_selected:string", "id_ubi_centro:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/buscar_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanCtrData"]
tags: ["misas", "buscar", "plan", "ctr", "data"]
estado_revision: "generado"
---

# Buscar Plan Ctr Data

Formulario buscador del plan de misas por centro (zonas + centros + periodo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/buscar_plan_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_BuscarPlanCtrDataData`):
  - `view` (`'sacd'|'centro'|'none'`)
  - `zonas_opciones` (`array`)
  - `zonas_selected` (`integer`)
  - `centros_opciones` (`array`)
  - `centros_selected` (`string`)
  - `id_ubi_centro` (`string`)

## Casos De Uso

- `src\misas\application\BuscarPlanCtrData`

## Frontend Relacionado

- `frontend/misas/controller/buscar_plan_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.