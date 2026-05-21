---
id: "encargossacd.encargo_lst_tipo_enc_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_lst_tipo_enc_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
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
estado_revision: "generado"
---

# Encargo Lst Tipo Enc Data

Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (`id_tipo_enc ~ ^grupo`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/encargo_lst_tipo_enc_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_lst_tipo_enc_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `grupo` | `mixed` | controller | No | controller |
| `id_tipo_enc` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoLstTipoEncDataData`):
  - `id` (`string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string`)

## Casos De Uso

- `src\encargossacd\application\EncargoLstTipoEncData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.