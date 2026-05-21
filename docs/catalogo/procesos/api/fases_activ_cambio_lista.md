---
id: "procesos.fases_activ_cambio_lista"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_FasesActivCambioListaData"
respuesta_data: ["error:string", "msg:string", "num_activ:integer", "num_ok:integer", "accion:string", "id_fase_nueva:string", "a_cabeceras:array", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio_lista.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioLista"]
tags: ["procesos", "fases", "activ", "cambio", "lista"]
estado_revision: "generado"
---

# Fases Activ Cambio Lista

Caso de uso: devuelve los datos estructurados para la tabla de actividades candidatas a cambiar de fase, segun filtros de tipo de actividad, dl_propia, periodo y accion (marcar/desmarcar). El frontend renderiza el formulario con `frontend\shared\web\Lista` + `web\Hash`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_FasesActivCambioListaData`):
  - `error` (`string`)
  - `msg` (`string`)
  - `num_activ` (`integer`)
  - `num_ok` (`integer`)
  - `accion` (`string`)
  - `id_fase_nueva` (`string`)
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)

## Casos De Uso

- `src\procesos\application\FasesActivCambioLista`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.