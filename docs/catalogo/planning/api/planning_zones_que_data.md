---
id: "planning.planning_zones_que_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_zones_que_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningZonesQueDataData"
respuesta_data: ["error:string, opciones_zonas: array<int|string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_zones_que.php"]
casos_uso: ["src\\planning\\application\\PlanningZonesQueData"]
tags: ["planning", "zones", "que", "data"]
estado_revision: "generado"
---

# Planning Zones Que Data

Opciones de zona + comprobación de permiso para `planning_zones_que`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_zones_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `planning_PlanningZonesQueDataData`):
  - `error` (`string, opciones_zonas: array<int|string, string>`)

## Casos De Uso

- `src\planning\application\PlanningZonesQueData`

## Frontend Relacionado

- `frontend/planning/controller/planning_zones_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.