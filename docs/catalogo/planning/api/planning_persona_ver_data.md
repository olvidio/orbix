---
id: "planning.planning_persona_ver_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_persona_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_persona_ver_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.obj_pau:string", "post.periodo:string", "post.sel:array", "post.sSeleccionados:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan fechas de periodo"]
frontend_referencias: ["frontend/planning/controller/planning_persona_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningPersonaVerData"]
tags: ["planning", "persona", "ver", "data"]
estado_revision: "revisado"
---

# Planning Persona Ver Data

Actividades de un conjunto de personas para el calendario de `planning_persona_ver`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve las personas seleccionadas (`sel` como lista o `sSeleccionados` CSV de `id_nom`),
las carga del repositorio `obj_pau` y devuelve `a_actividades` en vista plana (sin agrupar por centro).
El periodo se calcula en el controller con `Periodo::conCalendarioDesdeBackend()`.

## Endpoint

- URL: `/src/planning/planning_persona_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_persona_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller | No | Lista de `id_nom` seleccionados |
| `sSeleccionados` | `string` | controller | No | Alternativa CSV de `id_nom` (prioritaria si no vacía) |
| `obj_pau` | `string` | application | No | Colectivo de personas |
| `year` | `integer` | controller | No | Año del periodo |
| `periodo` | `string` | controller | No | Código de periodo |
| `empiezamin` | `string` | controller | No | Límite inferior inicio actividad |
| `empiezamax` | `string` | controller | No | Límite superior inicio actividad |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- `data.a_actividades` (`array`): slots de planning por persona.

## Permisos

- Sin control propio; autorización en frontend + menú.

## Errores conocidos

- `Faltan fechas de periodo` — periodo no resuelto en el controller.

## Casos De Uso

- `src\planning\application\PlanningPersonaVerData`

## Frontend Relacionado

- `frontend/planning/controller/planning_persona_ver.php`
