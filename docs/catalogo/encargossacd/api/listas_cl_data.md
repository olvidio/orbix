---
id: "encargossacd.listas_cl_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_cl_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_cl_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasClDataData"
respuesta_data: ["Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_cl.php"]
casos_uso: ["src\\encargossacd\\application\\ListasClData"]
tags: ["encargossacd", "listas", "cl", "data"]
estado_revision: "revisado"
---
# Listas Cl Data

Listado de cl para cr, restringido a los centros de la sss+. Sustituye la logica de `frontend/encargossacd/controller/listas_cl.php` (era una plantilla con SQL crudo). Devuelve el HTML completo listo para volcarlo al cliente; el frontend se limita a pasar `sf` y a echo del resultado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado «cl»: encargos colegiales.

## Endpoint

- URL: `/src/encargossacd/listas_cl_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_cl_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Claves: cabeceras + `Html` (doble `JSON.parse`).


## Permisos

Sin control propio; menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasClData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_cl.php`

