---
id: "procesos.tipo_activ_proceso_lista"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_TipoActivProcesoListaData"
respuesta_data: ["a_cabeceras:array", "a_tipos:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso_lista.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoLista"]
tags: ["procesos", "tipo", "activ", "proceso", "lista"]
estado_revision: "generado"
---

# Tipo Activ Proceso Lista

Caso de uso: devuelve el listado estructurado de tipos de actividad con el proceso propio / no-propio asignado. El frontend renderiza la tabla con `frontend\shared\web\Lista`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_TipoActivProcesoListaData`):
  - `a_cabeceras` (`array`)
  - `a_tipos` (`array`)

## Casos De Uso

- `src\procesos\application\TipoActivProcesoLista`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.