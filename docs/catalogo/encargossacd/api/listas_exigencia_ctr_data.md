---
id: "encargossacd.listas_exigencia_ctr_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_exigencia_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_exigencia_ctr_data.php"
entrada: ["post.ctr_igl:mixed", "post.sf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasExigenciaCtrDataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_exigencia_ctr.php"]
casos_uso: ["src\\encargossacd\\application\\ListasExigenciaCtrData"]
tags: ["encargossacd", "listas", "exigencia", "ctr", "data"]
estado_revision: "revisado"
---
# Listas Exigencia Ctr Data

Listado de exigencias SACD por centro/iglesia. Sustituye la logica de `frontend/encargossacd/controller/listas_exigencia_ctr.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado exigencia ctr (`sf`, `ctr_igl`): requisitos de atención por tipo de centro.

## Endpoint

- URL: `/src/encargossacd/listas_exigencia_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_exigencia_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctr_igl` | `mixed` | controller | No | controller |
| `sf` | `mixed` | controller | No | controller |

## Salida

- Claves: cabeceras + `Html` (doble `JSON.parse`).


## Permisos

Sin control propio; menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasExigenciaCtrData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_exigencia_ctr.php`

