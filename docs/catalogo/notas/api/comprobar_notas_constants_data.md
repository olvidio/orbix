---
id: "notas.comprobar_notas_constants_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/comprobar_notas_constants_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/comprobar_notas_constants_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_ComprobarNotasConstantsDataData"
respuesta_data: ["vo:array"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\notas\\application\\ComprobarNotasConstantsData"]
tags: ["notas", "comprobar", "constants", "data"]
estado_revision: "revisado"
---

# Comprobar Notas Constants Data

Constantes VO para consultas SQL de comprobar notas.

VO {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/comprobar_notas_constants_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/comprobar_notas_constants_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- `vo.NivelStgrId`, `vo.NotaSituacion` (enteros).
- Payload en `data` (schema `notas_ComprobarNotasConstantsDataData`):
  - `vo` (`array`)

## Objetivo funcional

Serializa `NivelStgrId` y `NotaSituacion` para el frontend legacy SQL.

## Permisos

- Menú comprobar datos n/agd.

## Casos De Uso

- `src\notas\application\ComprobarNotasConstantsData`

## Frontend Relacionado

- `frontend/notas/controller/comprobar_notas.php`.