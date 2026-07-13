---
id: "planning.planning_ctr_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_ctr_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_ctr_select_data.php"
entrada: ["post.ctr:string", "post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.sacd:string", "post.todos_agd:string", "post.todos_n:string", "post.todos_s:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan fechas de periodo", "No encuentro este ctr", "No encuentro personas para %s"]
frontend_referencias: ["frontend/planning/controller/planning_ctr_select.php"]
casos_uso: ["src\\planning\\application\\PlanningCtrSelectData"]
tags: ["planning", "ctr", "select", "data"]
estado_revision: "revisado"
---

# Planning Ctr Select Data

Personas y actividades agrupadas por centro para el calendario de `planning_ctr_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dos modos según `ctr`:

- **Centro concreto** (`ctr` no vacío): busca el centro por nombre (sin acentos) y lista personas
  activas de ese ctr. Acumula avisos en `msg_txt` si algún ctr no tiene personas.
- **Todos los centros** (`ctr` vacío): lista personas según `todos_n` / `todos_agd` / `todos_s`
  (tabla `n`, `a` o `s`).

Con las personas encontradas, `ActividadesDePersonaService` devuelve `a_actividades2` agrupado por centro.
El periodo se calcula en el controller con `Periodo::conCalendarioDesdeBackend()`.

## Endpoint

- URL: `/src/planning/planning_ctr_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_ctr_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `year` | `integer` | controller | No | Año del periodo |
| `periodo` | `string` | controller | No | Código de periodo calendario |
| `empiezamin` | `string` | controller | No | Límite inferior de inicio de actividad |
| `empiezamax` | `string` | controller | No | Límite superior de inicio de actividad |
| `ctr` | `string` | application | No | Nombre de centro; vacío = todos |
| `sacd` | `string` | application | No | Vacío excluye sacd (`sacd=f` en filtro) |
| `todos_n` | `string` | application | No | Checkbox numerarios (tabla `n`) |
| `todos_agd` | `string` | application | No | Checkbox agd (tabla `a`) |
| `todos_s` | `string` | application | No | Checkbox supernumerarios (tabla `s`) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- `data`:
  - `msg_txt` (`string`): avisos HTML (ctr sin personas, ctr no encontrado).
  - `cabecera_title` (`string`): título de la vista.
  - `a_actividades2` (`array`): actividades por persona/centro en el periodo.

## Permisos

- Sin control propio; autorización en frontend + menú.

## Errores conocidos

- `Faltan fechas de periodo` — periodo no resuelto en el controller.
- `No encuentro este ctr` — nombre de centro sin coincidencia.
- `No encuentro personas para %s` — ctr válido pero sin personas (mensaje acumulable).

## Casos De Uso

- `src\planning\application\PlanningCtrSelectData`

## Frontend Relacionado

- `frontend/planning/controller/planning_ctr_select.php`
