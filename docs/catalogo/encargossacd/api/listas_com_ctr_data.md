---
id: "encargossacd.listas_com_ctr_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_com_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_com_ctr_data.php"
entrada: ["post.sfsv:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasComCtrDataData"
respuesta_data: ["array_atn_sacd:array", "origen_txt:string", "lugar_fecha:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_com_ctr.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComCtrData"]
tags: ["encargossacd", "listas", "com", "ctr", "data"]
estado_revision: "revisado"
---
# Listas Com Ctr Data

Datos para la comunicacion a los centros. Sustituye la logica de `frontend/encargossacd/controller/listas_com_ctr.php`. El modelo de salida replica el consumido por la vista `listas_com_ctr.phtml`: - `array_atn_sacd[nombre_ubi]` con titular, suplente, colaboradores y el texto de comunicacion traducido al idioma del idioma actual. - `origen_txt` cabecera de emisor y `lugar_fecha` pie.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado comunicación centros (`sfsv`: sv|sf). HTML para impresión/envío.

## Endpoint

- URL: `/src/encargossacd/listas_com_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sfsv` | `mixed` | controller | No | controller |

## Salida

- Claves: cabeceras + `Html` (doble `JSON.parse`).


## Permisos

Sin control propio; menú listados.

## Casos De Uso

- `src\encargossacd\application\ListasComCtrData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_com_ctr.php`

