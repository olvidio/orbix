---
id: "encargossacd.horario_update_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/horario_update_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/horario_update_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoHorarioUpdateData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/horario_update.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioUpdate"]
tags: ["encargossacd", "horario", "update", "data"]
estado_revision: "generado"
---

# Horario Update Data

Alta/edición/baja de horario de encargo (tabla encargo_horario).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/horario_update_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_update_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoHorarioUpdateData`):
  - `ok` (`true`)

## Casos De Uso

- `src\encargossacd\application\EncargoHorarioUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/horario_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.