---
id: "encargossacd.sacd_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php"
entrada: ["post.filtro_sacd:mixed", "post.id_nom:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdSelectDataData"
respuesta_data: ["opciones:array", "selected:integer", "label_prefix:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdSelectData"]
tags: ["encargossacd", "sacd", "select", "data"]
estado_revision: "revisado"
---
# Sacd Select Data

Opciones para el desplegable de SACDs filtrados por tabla (`sacd_ficha_ajax?que=get_select`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Opciones desplegable SACD filtradas por `filtro_sacd` (tabla). Sucesor de `sacd_ficha_ajax?que=get_select`.

## Endpoint

- URL: `/src/encargossacd/sacd_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_sacd` | `mixed` | controller | No | controller |
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Claves: `opciones`, `selected`, `label_prefix` (doble `JSON.parse`).


## Permisos

Sin control propio; ficha sacd / ausencias.

## Casos De Uso

- `src\encargossacd\application\SacdSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

