---
id: "planning.planning_casa_ver_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_casa_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php"
entrada: ["post.cdc_sel:integer", "post.f_fin_iso:string", "post.f_ini_iso:string", "post.sSeleccionados:string", "post.sin_activ:integer"]
entrada_obligatoria: ["post.f_ini_iso", "post.f_fin_iso"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan fechas de periodo (f_ini_iso / f_fin_iso)."]
frontend_referencias: ["frontend/planning/controller/planning_casa_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaVerData"]
tags: ["planning", "casa", "ver", "data"]
estado_revision: "revisado"
---

# Planning Casa Ver Data

Actividades agrupadas por casa y periodos de ocupación para renderizar el calendario de
`planning_casa_ver`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dado un grupo de casas (`cdc_sel`) y un intervalo ISO, devuelve:

- `a_actividades`: actividades por casa/ubi en el periodo (vía `ActividadesPorCasasService`).
- `casa_periodos_por_ubi`: periodos de ocupación por ubi para sombrear el calendario.

Si `cdc_sel = 9` (selección manual), usa `sSeleccionados` (CSV de id cdc) como lista de casas.

## Endpoint

- URL: `/src/planning/planning_casa_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_ini_iso` | `string` | application | Sí | Inicio del planning (ISO `Y-m-d`) |
| `f_fin_iso` | `string` | application | Sí | Fin del planning (ISO `Y-m-d`) |
| `cdc_sel` | `integer` | application | No | Modo de grupo de casas (`CasasQue`); `9` = lista manual |
| `sSeleccionados` | `string` | application | No | CSV de id cdc cuando `cdc_sel = 9` |
| `sin_activ` | `integer` | application | No | `1` incluye casas sin actividad en el periodo |

El frontend resuelve `year`/`periodo`/`empiezamin`/`empiezamax` a `f_ini_iso`/`f_fin_iso` antes de llamar.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- `data`:
  - `a_actividades` (`array`): mapa casa → actividades con slots de planning.
  - `casa_periodos_por_ubi` (`array`): periodos `{iso_ini, iso_fin, sfsv}` por ubi.

## Permisos

- Sin control propio en el caso de uso; autorización en frontend + menú (`$_SESSION['oPerm']`).

## Errores conocidos

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

## Casos De Uso

- `src\planning\application\PlanningCasaVerData`

## Frontend Relacionado

- `frontend/planning/controller/planning_casa_ver.php`
