---
id: "encargossacd.sacd_ausencias_get_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_get_data"
metodos: ["GET", "POST"]
operacion: "form_data"
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
estado_revision: "revisado"
---
# Sacd Ausencias Get Data

Datos para la ficha de ausencias de un SACD (`frontend/encargossacd/controller/sacd_ausencias_get.php`). Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD. Con `historial=1` incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tipos de ausencia (encargos 7/4) y filas del SACD. `historial=1` incluye todas; sin historial solo vigentes.

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_get_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_get_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `historial` | `mixed` | controller | No | controller |
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Claves: `array_tipo_ausencias`, `filas[]` (doble `JSON.parse`).


## Permisos

Sin control propio; menú ausencias.

## Casos De Uso

- `src\encargossacd\application\SacdAusenciasGetData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ausencias_get.php`

