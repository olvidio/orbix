---
id: "notas.asignaturas_search"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asignaturas_search"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/asignaturas_search.php"
entrada: ["post.search:string"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\AsignaturasSearchData"]
tags: ["notas", "asignaturas", "search"]
estado_revision: "revisado"
---

# Asignaturas Search

Autocompletado de asignaturas en el formulario de acta.

Autocomplete jQuery-UI.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asignaturas_search`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/asignaturas_search.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `search` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Opciones autocomplete en `data` (doble `JSON.parse`).

## Objetivo funcional

Búsqueda por `search` en catálogo de asignaturas.

## Permisos

- Contexto `acta_ver`.

## Casos De Uso

- `src\notas\application\AsignaturasSearchData`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php`.