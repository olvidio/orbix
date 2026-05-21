---
id: "procesos.fases_activ_cambio_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_get.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_FasesActivCambioGetData"
respuesta_data: ["id:string", "opciones:array", "selected:string", "blanco:boolean", "action:string"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioGet"]
tags: ["procesos", "fases", "activ", "cambio", "get"]
estado_revision: "generado"
---

# Fases Activ Cambio Get

Caso de uso: devuelve las fases posibles para el `id_tipo_activ` y la `dl_propia` actual, incluyendo la opcion seleccionada por `id_fase_sel`. Respuesta conforme al contrato de `refactor.md` para desplegables (payload JSON con `id`, `opciones`, `selected`, `blanco`, `action`). El frontend construye el `<select>` con el helper JS estandar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_get.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_FasesActivCambioGetData`):
  - `id` (`string`)
  - `opciones` (`array`)
  - `selected` (`string`)
  - `blanco` (`boolean`)
  - `action` (`string`)

## Casos De Uso

- `src\procesos\application\FasesActivCambioGet`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.