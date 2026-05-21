---
id: "planning.planning_persona_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_persona_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_persona_select_data.php"
entrada: ["post.apellido1:string", "post.apellido2:string", "post.centro:string", "post.na:string", "post.nombre:string", "post.obj_pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_persona_select.php"]
casos_uso: ["src\\planning\\application\\PlanningPersonaSelectData"]
tags: ["planning", "persona", "select", "data"]
estado_revision: "generado"
---

# Planning Persona Select Data

Listado de personas para `planning_persona_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_persona_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_persona_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `apellido1` | `string` | application | No | application |
| `apellido2` | `string` | application | No | application |
| `centro` | `string` | application | No | application |
| `na` | `string` | application | No | application |
| `nombre` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\planning\application\PlanningPersonaSelectData`

## Frontend Relacionado

- `frontend/planning/controller/planning_persona_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.