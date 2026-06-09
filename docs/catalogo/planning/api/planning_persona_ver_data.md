---
id: "planning.planning_persona_ver_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_persona_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_persona_ver_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.obj_pau:string", "post.periodo:string", "post.sel:array", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningPersonaVerDataData"
respuesta_data: ["a_actividades:array"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_persona_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningPersonaVerData"]
tags: ["planning", "persona", "ver", "data"]
estado_revision: "generado"
---

# Planning Persona Ver Data

Actividades por persona (vista plana) para `planning_persona_ver`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_persona_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_persona_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `obj_pau` | `string` | application | No | application |
| `periodo` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |
| `year` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `planning_PlanningPersonaVerDataData`):
  - `a_actividades` (`array`)

## Casos De Uso

- `src\planning\application\PlanningPersonaVerData`

## Frontend Relacionado

- `frontend/planning/controller/planning_persona_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.