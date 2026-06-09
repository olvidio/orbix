---
id: "encargossacd.sacd_ausencias_get_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_get_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_get_data.php"
entrada: ["post.historial:mixed", "post.id_nom:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdAusenciasGetDataData"
respuesta_data: ["array_tipo_ausencias:array", "filas:list<array{", "id_enc:integer", "id_tipo_enc:integer", "desc_enc:string", "id_item:integer", "inicio:?string", "fin:?string", "dedic_m:string", "dedic_t:string", "dedic_v:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_get.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasGetData"]
tags: ["encargossacd", "sacd", "ausencias", "get", "data"]
estado_revision: "generado"
---

# Sacd Ausencias Get Data

Datos para la ficha de ausencias de un SACD (`frontend/encargossacd/controller/sacd_ausencias_get.php`). Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD. Con `historial=1` incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_get_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_get_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `historial` | `mixed` | controller | No | controller |
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_SacdAusenciasGetDataData`):
  - `array_tipo_ausencias` (`array`)
  - `filas` (`list<array{`)
  - `id_enc` (`integer`)
  - `id_tipo_enc` (`integer`)
  - `desc_enc` (`string`)
  - `id_item` (`integer`)
  - `inicio` (`?string`)
  - `fin` (`?string`)
  - `dedic_m` (`string`)
  - `dedic_t` (`string`)
  - `dedic_v` (`string`)

## Casos De Uso

- `src\encargossacd\application\SacdAusenciasGetData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ausencias_get.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.