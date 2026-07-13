---
id: "notas.posibles_preceptores_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/posibles_preceptores_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/posibles_preceptores_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php", "frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PosiblesPreceptoresData"]
tags: ["notas", "posibles", "preceptores", "data"]
estado_revision: "revisado"
---

# Posibles Preceptores Data

Preceptores posibles para una nota.

Devuelve el desplegable de posibles preceptores (profesores STGR) con el contrato estandar de refactor.md.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/posibles_preceptores_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/posibles_preceptores_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Opciones en `data`.

## Objetivo funcional

Desplegable de preceptores según contexto de la nota.

## Permisos

- Formulario nota persona.

## Casos De Uso

- `src\notas\application\PosiblesPreceptoresData`

## Frontend Relacionado

- `form_notas_de_una_persona`.