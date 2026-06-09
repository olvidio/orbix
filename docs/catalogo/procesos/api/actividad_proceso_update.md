---
id: "procesos.actividad_proceso_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php"
entrada: ["post.completado:string", "post.id_item:integer", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la tarea del proceso", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoUpdate"]
tags: ["procesos", "actividad", "proceso", "update"]
estado_revision: "generado"
---

# Actividad Proceso Update

Caso de uso: guarda el estado (completado/observaciones) de una tarea del proceso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/actividad_proceso_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `completado` | `string` | application | No | application |
| `id_item` | `integer` | application | No | application |
| `observ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarea del proceso`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\procesos\application\ActividadProcesoUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.