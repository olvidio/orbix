---
id: "asignaturas.asignaturas_map_data"
tipo: "endpoint"
modulo: "asignaturas"
url: "/src/asignaturas/asignaturas_map_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asignaturas/infrastructure/ui/http/controllers/asignaturas_map_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asignaturas_AsignaturasMapDataData"
respuesta_data: ["a_asignaturas:array"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\asignaturas\\application\\AsignaturasMapData"]
tags: ["asignaturas", "map", "data"]
estado_revision: "generado"
---

# Asignaturas Map Data

Mapa id_asignatura => nombre_corto para pantallas que no deben usar el contenedor en frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asignaturas/asignaturas_map_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asignaturas/infrastructure/ui/http/controllers/asignaturas_map_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asignaturas_AsignaturasMapDataData`):
  - `a_asignaturas` (`array`)

## Casos De Uso

- `src\asignaturas\application\AsignaturasMapData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.