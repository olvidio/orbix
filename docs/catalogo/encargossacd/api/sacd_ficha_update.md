---
id: "encargossacd.sacd_ficha_update"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ficha_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_update.php"
entrada: ["post.dedic_m:mixed", "post.dedic_t:mixed", "post.dedic_v:mixed", "post.enc_num:integer", "post.id_enc:mixed", "post.id_nom:integer", "post.id_tipo_enc:mixed", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdFichaUpdateData"
respuesta_data: ["error:string, mensajes: string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdFichaUpdate"]
tags: ["encargossacd", "sacd", "ficha", "update"]
estado_revision: "generado"
---

# Sacd Ficha Update

Mutacion de la ficha de encargos de un SACD (`sacd_ficha_ajax?que=update`). Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/sacd_ficha_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dedic_m` | `mixed` | application | No | application |
| `dedic_t` | `mixed` | application | No | application |
| `dedic_v` | `mixed` | application | No | application |
| `enc_num` | `integer` | application | No | application |
| `id_enc` | `mixed` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_tipo_enc` | `mixed` | application | No | application |
| `observ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_SacdFichaUpdateData`):
  - `error` (`string, mensajes: string`)

## Casos De Uso

- `src\encargossacd\application\SacdFichaUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.