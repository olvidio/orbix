---
id: "encargossacd.sacd_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php"
entrada: ["post.filtro_sacd:mixed", "post.id_nom:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdSelectDataData"
respuesta_data: ["opciones:array", "selected:integer", "label_prefix:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdSelectData"]
tags: ["encargossacd", "sacd", "select", "data"]
estado_revision: "generado"
---

# Sacd Select Data

Opciones para el desplegable de SACDs filtrados por tabla (`sacd_ficha_ajax?que=get_select`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/sacd_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_sacd` | `mixed` | controller | No | controller |
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_SacdSelectDataData`):
  - `opciones` (`array`)
  - `selected` (`integer`)
  - `label_prefix` (`string`)

## Casos De Uso

- `src\encargossacd\application\SacdSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.