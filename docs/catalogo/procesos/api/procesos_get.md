---
id: "procesos.procesos_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_get.php"
entrada: ["post.id_tipo_proceso:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ProcesosGetData"
respuesta_data: ["aPadres:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_get.php", "frontend/procesos/controller/procesos_get_listado.php", "frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosGet"]
tags: ["procesos", "get"]
estado_revision: "generado"
---

# Procesos Get

Caso de uso: estructura padres/hijos del árbol de fases del proceso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_proceso` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ProcesosGetData`):
  - `aPadres` (`array`)

## Casos De Uso

- `src\procesos\application\ProcesosGet`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_get.php`
- `frontend/procesos/controller/procesos_get_listado.php`
- `frontend/procesos/controller/procesos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.