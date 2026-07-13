---
id: "notas.examinadores_search"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/examinadores_search"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/examinadores_search.php"
entrada: ["post.search:string"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ExaminadoresSearchData"]
tags: ["notas", "examinadores", "search"]
estado_revision: "revisado"
---

# Examinadores Search

Autocompletado de examinadores para el formulario de acta.

Autocomplete jQuery-UI.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/examinadores_search`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/examinadores_search.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `search` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Payload en `data` (doble `JSON.parse`): lista de sugerencias `{label, value}` o equivalente del builder.

## Objetivo funcional

Búsqueda por texto (`search`) de personas examinadoras; devuelve opciones para jQuery UI autocomplete en `acta_ver`.

## Permisos

- Contexto `acta_ver`; sin permiso propio.

## Casos De Uso

- `src\notas\application\ExaminadoresSearchData`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php` (`fnjs_autocomplete_exam`).