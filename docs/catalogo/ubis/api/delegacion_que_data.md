---
id: "ubis.delegacion_que_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/delegacion_que_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/delegacion_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DelegacionQueDataData"
respuesta_data: ["opciones_dl_destino:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/delegacion_que.php"]
casos_uso: ["src\\ubis\\application\\DelegacionQueData"]
tags: ["ubis", "delegacion", "que", "data"]
estado_revision: "generado"
---

# Delegacion Que Data

Opciones del formulario delegaciones (traslado de ubis).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/delegacion_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/delegacion_que_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_DelegacionQueDataData`):
  - `opciones_dl_destino` (`array`)

## Casos De Uso

- `src\ubis\application\DelegacionQueData`

## Frontend Relacionado

- `frontend/ubis/controller/delegacion_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.