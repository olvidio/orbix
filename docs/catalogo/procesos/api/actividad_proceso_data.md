---
id: "procesos.actividad_proceso_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_data.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ActividadProcesoDataData"
respuesta_data: ["id_activ:int, nom_activ: string"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoData"]
tags: ["procesos", "actividad", "proceso", "data"]
estado_revision: "generado"
---

# Actividad Proceso Data

Caso de uso: datos para la pantalla `actividad_proceso`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/actividad_proceso_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ActividadProcesoDataData`):
  - `id_activ` (`int, nom_activ: string`)

## Casos De Uso

- `src\procesos\application\ActividadProcesoData`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.