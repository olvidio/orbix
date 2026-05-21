---
id: "planning.planning_ctr_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_ctr_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_ctr_select_data.php"
entrada: ["post.ctr:string", "post.sacd:string", "post.todos_agd:string", "post.todos_n:string", "post.todos_s:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningCtrSelectDataData"
respuesta_data: ["msg_txt:string, cabecera_title: string, a_actividades2: array"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_ctr_select.php"]
casos_uso: ["src\\planning\\application\\PlanningCtrSelectData"]
tags: ["planning", "ctr", "select", "data"]
estado_revision: "generado"
---

# Planning Ctr Select Data

Personas + actividades agrupadas por centro para `planning_ctr_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_ctr_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_ctr_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctr` | `string` | application | No | application |
| `sacd` | `string` | application | No | application |
| `todos_agd` | `string` | application | No | application |
| `todos_n` | `string` | application | No | application |
| `todos_s` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `planning_PlanningCtrSelectDataData`):
  - `msg_txt` (`string, cabecera_title: string, a_actividades2: array`)

## Casos De Uso

- `src\planning\application\PlanningCtrSelectData`

## Frontend Relacionado

- `frontend/planning/controller/planning_ctr_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.