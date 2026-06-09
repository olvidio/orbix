---
id: "procesos.actividad_proceso_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_get.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ActividadProcesoGetData"
respuesta_data: ["error:string, a_rows: list<array<string, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php", "frontend/procesos/controller/actividad_proceso_get.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoGet"]
tags: ["procesos", "actividad", "proceso", "get"]
estado_revision: "generado"
---

# Actividad Proceso Get

Caso de uso: tareas del proceso para un id_activ (estructura + permiso edición).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/actividad_proceso_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ActividadProcesoGetData`):
  - `error` (`string, a_rows: list<array<string, mixed>>`)

## Casos De Uso

- `src\procesos\application\ActividadProcesoGet`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php`
- `frontend/procesos/controller/actividad_proceso_get.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.