---
id: "encargossacd.encargo_lst_tipo_enc_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_lst_tipo_enc_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_lst_tipo_enc_data.php"
entrada: ["post.grupo:mixed", "post.id_tipo_enc:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoLstTipoEncDataData"
respuesta_data: ["id:string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoLstTipoEncData"]
tags: ["encargossacd", "encargo", "lst", "tipo", "enc", "data"]
estado_revision: "revisado"
---
# Encargo Lst Tipo Enc Data

Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (`id_tipo_enc ~ ^grupo`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Opciones de tipos de encargo filtradas por `grupo` para desplegable en `encargo_ver`.

## Endpoint

- URL: `/src/encargossacd/encargo_lst_tipo_enc_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_lst_tipo_enc_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `grupo` | `mixed` | controller | No | controller |
| `id_tipo_enc` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Claves: `opciones`, `selected` (doble `JSON.parse`).


## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoLstTipoEncData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

