---
id: "planning.planning_casa_ver_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_casa_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php"
entrada: ["post.cdc_sel:integer", "post.f_fin_iso:string", "post.f_ini_iso:string", "post.sSeleccionados:string", "post.sin_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningCasaVerDataData"
respuesta_data: ["a_actividades:array"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_casa_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaVerData"]
tags: ["planning", "casa", "ver", "data"]
estado_revision: "generado"
---

# Planning Casa Ver Data

Dataset para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_casa_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cdc_sel` | `integer` | application | No | application |
| `f_fin_iso` | `string` | application | No | application |
| `f_ini_iso` | `string` | application | No | application |
| `sSeleccionados` | `string` | application | No | application |
| `sin_activ` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `planning_PlanningCasaVerDataData`):
  - `a_actividades` (`array`)

## Casos De Uso

- `src\planning\application\PlanningCasaVerData`

## Frontend Relacionado

- `frontend/planning/controller/planning_casa_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.