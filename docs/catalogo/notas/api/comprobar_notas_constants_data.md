---
id: "notas.comprobar_notas_constants_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/comprobar_notas_constants_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
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
estado_revision: "generado"
---

# Comprobar Notas Constants Data

VO {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/comprobar_notas_constants_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/comprobar_notas_constants_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_ComprobarNotasConstantsDataData`):
  - `vo` (`array`)

## Casos De Uso

- `src\notas\application\ComprobarNotasConstantsData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.