---
id: "planning.planning_casa_que_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_casa_que_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "planning_PlanningCasaQueFormDataData"
respuesta_data: ["filtro:array"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_casa_que.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaQueFormData"]
tags: ["planning", "casa", "que", "data"]
estado_revision: "generado"
---

# Planning Casa Que Data

Dataset para montar CasasQue en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/planning/planning_casa_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `planning_PlanningCasaQueFormDataData`):
  - `filtro` (`array`)

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\planning\application\PlanningCasaQueFormData`

## Frontend Relacionado

- `frontend/planning/controller/planning_casa_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.