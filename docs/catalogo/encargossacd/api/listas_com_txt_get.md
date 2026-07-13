---
id: "encargossacd.listas_com_txt_get"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_com_txt_get"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_get.php"
entrada: ["post.clave:mixed", "post.idioma:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasComTxtGetData"
respuesta_data: ["texto:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_com_txt.php", "frontend/encargossacd/controller/listas_com_txt_get.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComTxtGet"]
tags: ["encargossacd", "listas", "com", "txt", "get"]
estado_revision: "revisado"
---
# Listas Com Txt Get

Lectura del texto de comunicacion para un par (clave, idioma). Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lee texto de comunicación por `clave` + `idioma`.

## Endpoint

- URL: `/src/encargossacd/listas_com_txt_get`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `mixed` | controller | No | controller |
| `idioma` | `mixed` | controller | No | controller |

## Salida

- Claves: `texto` (doble `JSON.parse`).


## Efectos colaterales

- Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

## Permisos

Sin control propio; menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasComTxtGet`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_com_txt.php`
- `frontend/encargossacd/controller/listas_com_txt_get.php`

