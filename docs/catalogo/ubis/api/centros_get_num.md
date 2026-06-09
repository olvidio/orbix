---
id: "ubis.centros_get_num"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_get_num"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_get_num.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosGetNumDataData"
respuesta_data: ["a_cabeceras:list<mixed>, a_valores: array<int, array<int, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_get_num.php"]
casos_uso: ["src\\ubis\\application\\CentrosGetNumData"]
tags: ["ubis", "centros", "get", "num"]
estado_revision: "generado"
---

# Centros Get Num

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/centros_get_num`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_num.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_CentrosGetNumDataData`):
  - `a_cabeceras` (`list<mixed>, a_valores: array<int, array<int, mixed>>`)

## Casos De Uso

- `src\ubis\application\CentrosGetNumData`

## Frontend Relacionado

- `frontend/ubis/controller/centros_get_num.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.