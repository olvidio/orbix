---
id: "procesos.actividad_proceso_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoUpdate"]
tags: ["procesos", "actividad", "proceso", "update"]
estado_revision: "generado"
---

# Actividad Proceso Update

Caso de uso: guarda el estado (completado/observaciones) de una tarea concreta (id_item) del proceso de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/actividad_proceso_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `hay un error, no se ha guardado`

## Casos De Uso

- `src\procesos\application\ActividadProcesoUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.