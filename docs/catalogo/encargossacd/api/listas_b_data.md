---
id: "encargossacd.listas_b_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_b_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_b_data.php"
entrada: ["post.sf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasBDataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_b.php"]
casos_uso: ["src\\encargossacd\\application\\ListasBData"]
tags: ["encargossacd", "listas", "b", "data"]
estado_revision: "revisado"
---
# Listas B Data

Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b). Sustituye la logica de `frontend/encargossacd/controller/listas_b.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado «b» (ref. cr 1/14, 10, b): iglesias/oc/lp con capellanes.

## Endpoint

- URL: `/src/encargossacd/listas_b_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_b_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sf` | `mixed` | controller | No | controller |

## Salida

- Claves: cabeceras + `Html` (doble `JSON.parse`).


## Permisos

Sin control propio; menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasBData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_b.php`

