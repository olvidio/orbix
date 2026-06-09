---
id: "planning.planning_zones_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_zones_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php"
entrada: ["post.actividad:string", "post.id_zona:string", "post.propuesta:string", "post.trimestre:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningZonesSelectDataData"
respuesta_data: ["actividades_por_zona:array", "cabeceras_por_zona:array", "zonas:integer", "titulo:string", "planning_ini_iso:string", "planning_fin_iso:string"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_zones_select.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesSelectData"]
tags: ["planning", "zones", "select", "data"]
estado_revision: "generado"
---

# Planning Zones Select Data

Dataset para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_zones_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `actividad` | `string` | application | No | application |
| `id_zona` | `string` | application | No | application |
| `propuesta` | `string` | application | No | application |
| `trimestre` | `integer` | application | No | application |
| `year` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `planning_PlanningZonesSelectDataData`):
  - `actividades_por_zona` (`array`)
  - `cabeceras_por_zona` (`array`)
  - `zonas` (`integer`)
  - `titulo` (`string`)
  - `planning_ini_iso` (`string`)
  - `planning_fin_iso` (`string`)

## Casos De Uso

- `src\planning\application\PlanningZonesSelectData`

## Frontend Relacionado

- `frontend/planning/controller/planning_zones_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.