---
id: "notas.acta_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_select_data.php"
entrada: ["post.acta:string", "post.mes_fin_stgr:integer", "post.titulo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_ActaSelectDataData"
respuesta_data: ["titulo:string, a_asignaturas: array<int|string, string|null>, actas: list<array{acta:string, f_acta:?string, id_asignatura:int, has_pdf:bool}>"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_select.php"]
casos_uso: ["src\\notas\\application\\ActaSelectData"]
tags: ["notas", "acta", "select", "data"]
estado_revision: "revisado"
---

# Acta Select Data

Lista de actas y mapa de asignaturas para `acta_select` (frontend sin repositorios).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | controller | No | controller |
| `mes_fin_stgr` | `integer` | controller | No | controller |
| `titulo` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Forma: `standard_envelope_string_data`
- Payload en `data`:
  - `titulo` (`string`)
  - `a_asignaturas` (`array<int|string, string|null>`)
  - `actas` (lista `{acta, f_acta, id_asignatura, has_pdf}`)

## Objetivo funcional

Sin `acta` en filtro: últimas 20 actas del curso actual. Con `acta`: búsqueda por patrón (soporta prefijo DL y año). Incluye `has_pdf` por fila.

## Permisos

- Menú actas; edición solo DL con `est`.

## Casos De Uso

- `src\notas\application\ActaSelectData`

## Frontend Relacionado

- `frontend/notas/controller/acta_select.php`.