---
id: "procesos.procesos_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_update.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php", "frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosUpdate"]
tags: ["procesos", "update"]
estado_revision: "generado"
---

# Procesos Update

Caso de uso: guarda una tarea_proceso (fase/tarea/responsable/status y fases_previas) del proceso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_update.php`

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

- `src\procesos\application\ProcesosUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php`
- `frontend/procesos/controller/procesos_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.