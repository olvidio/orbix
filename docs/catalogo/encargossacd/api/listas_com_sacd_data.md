---
id: "encargossacd.listas_com_sacd_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_com_sacd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_com_sacd_data.php"
entrada: ["post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasComSacdDataData"
respuesta_data: ["array_modo:array", "lugar_fecha:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_com_sacd.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComSacdData"]
tags: ["encargossacd", "listas", "com", "sacd", "data"]
estado_revision: "revisado"
---
# Listas Com Sacd Data

Datos para la comunicacion a los SACD. Sustituye la logica de `frontend/encargossacd/controller/listas_com_sacd.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado comunicación SACD. `sel` filtra sacds (tokens desplegable).

## Endpoint

- URL: `/src/encargossacd/listas_com_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | controller | No | controller |

## Salida

- Claves: cabeceras + `Html` (doble `JSON.parse`).


## Permisos

Sin control propio; menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasComSacdData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_com_sacd.php`

