---
id: "ubis.lista_ctrs_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/lista_ctrs_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/lista_ctrs_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosSListaDataData"
respuesta_data: ["a_cabeceras:list<string>, a_valores: array<int, array<int, int|string>>, num_total_s: int"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/lista_ctrs.php"]
casos_uso: ["src\\ubis\\application\\CentrosSListaData"]
tags: ["ubis", "lista", "ctrs", "data"]
estado_revision: "generado"
---

# Lista Ctrs Data

Listado de centros de tipo 's' (sacerdotes) con el número de personas s asignadas en cada uno, y el total global.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/lista_ctrs_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/lista_ctrs_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_CentrosSListaDataData`):
  - `a_cabeceras` (`list<string>, a_valores: array<int, array<int, int|string>>, num_total_s: int`)

## Casos De Uso

- `src\ubis\application\CentrosSListaData`

## Frontend Relacionado

- `frontend/ubis/controller/lista_ctrs.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.