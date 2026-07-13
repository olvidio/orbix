---
id: "planning.planning_zones_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_zones_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php"
entrada: ["post.actividad:string", "post.id_zona:string", "post.propuesta:string", "post.trimestre:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/planning/controller/planning_zones_select.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesSelectData"]
tags: ["planning", "zones", "select", "data"]
estado_revision: "revisado"
---

# Planning Zones Select Data

Actividades agrupadas por zona SACD para el calendario de `planning_zones_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Invoca `ActividadesPorZonasService` con zona, trimestre, año, filtro de actividad y flag
`propuesta` (calendario en estudio). Serializa fechas del periodo como `planning_ini_iso` /
`planning_fin_iso` y devuelve la cuadrícula por zonas.

## Endpoint

- URL: `/src/planning/planning_zones_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `string` | application | No | Zona SACD; vacío puede significar todas según servicio |
| `trimestre` | `integer` | application | No | Trimestre del periodo |
| `year` | `integer` | application | No | Año |
| `actividad` | `string` | application | No | Filtro por tipo/nombre de actividad |
| `propuesta` | `string` | application | No | `true` → calendario propuesta (`propuesta_calendario`) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- `data`:
  - `actividades_por_zona` (`array`): slots por zona.
  - `cabeceras_por_zona` (`array`): títulos de columna por zona.
  - `zonas` (`integer`): número de zonas mostradas.
  - `titulo` (`string`): título del planning.
  - `planning_ini_iso` / `planning_fin_iso` (`string`): límites del periodo.

## Permisos

- Sin control propio en el caso de uso; alcance de zonas ya filtrado en `planning_zones_que`.

## Casos De Uso

- `src\planning\application\PlanningZonesSelectData`

## Frontend Relacionado

- `frontend/planning/controller/planning_zones_select.php`
