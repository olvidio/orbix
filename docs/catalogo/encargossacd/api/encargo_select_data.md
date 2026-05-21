---
id: "encargossacd.encargo_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_select_data.php"
entrada: ["post.desc_enc:mixed", "post.id_tipo_enc:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoSelectDataData"
respuesta_data: ["filas:list<array{", "id_enc:integer", "sf_sv:integer", "idioma_enc:string", "id_ubi:integer", "desc_enc:string", "desc_lugar:string", "seccion:string", "nombre_ubi:string", "idioma:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_select.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSelectData"]
tags: ["encargossacd", "encargo", "select", "data"]
estado_revision: "generado"
---

# Encargo Select Data

Datos para la lista de encargos (`encargo_select`). El frontend construye la `frontend\shared\web\Lista` y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/encargo_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_enc` | `mixed` | controller | No | controller |
| `id_tipo_enc` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoSelectDataData`):
  - `filas` (`list<array{`)
  - `id_enc` (`integer`)
  - `sf_sv` (`integer`)
  - `idioma_enc` (`string`)
  - `id_ubi` (`integer`)
  - `desc_enc` (`string`)
  - `desc_lugar` (`string`)
  - `seccion` (`string`)
  - `nombre_ubi` (`string`)
  - `idioma` (`string`)

## Casos De Uso

- `src\encargossacd\application\EncargoSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.