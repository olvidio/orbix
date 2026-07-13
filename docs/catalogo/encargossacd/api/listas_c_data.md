---
id: "encargossacd.listas_c_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_c_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_c_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasCDataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_c.php"]
casos_uso: ["src\\encargossacd\\application\\ListasCData"]
tags: ["encargossacd", "listas", "c", "data"]
estado_revision: "revisado"
---
# Listas C Data

Genera el listado de atencion SACD "c" (cr 9/05, Anexo2, 9.4 c). Sustituye la logica de `frontend/encargossacd/controller/listas_c.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado «c» (ref. cr 1/14, 10, d): encargos por población/grupo. Oculta columnas sensibles si no `have_perm_oficina('vcsd'|'des')`.

## Endpoint

- URL: `/src/encargossacd/listas_c_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_c_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Claves: cabeceras + `Html` (doble `JSON.parse`).


## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

## Casos De Uso

- `src\encargossacd\application\ListasCData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_c.php`

