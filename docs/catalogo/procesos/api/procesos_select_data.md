---
id: "procesos.procesos_select_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_select_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ProcesosSelectDataData"
respuesta_data: ["a_tipos_proceso:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosSelectData"]
tags: ["procesos", "select", "data"]
estado_revision: "generado"
---

# Procesos Select Data

Caso de uso: datos para la pantalla `procesos_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_select_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ProcesosSelectDataData`):
  - `a_tipos_proceso` (`array`)

## Casos De Uso

- `src\procesos\application\ProcesosSelectData`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.