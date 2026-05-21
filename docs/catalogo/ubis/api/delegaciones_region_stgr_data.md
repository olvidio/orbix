---
id: "ubis.delegaciones_region_stgr_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/delegaciones_region_stgr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/delegaciones_region_stgr_data.php"
entrada: ["post.region_stgr:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DelegacionesRegionStgrDataData"
respuesta_data: ["a_delegaciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/resumen_anual.php"]
casos_uso: ["src\\ubis\\application\\DelegacionesRegionStgrData"]
tags: ["ubis", "delegaciones", "region", "stgr", "data"]
estado_revision: "generado"
---

# Delegaciones Region Stgr Data

Delegaciones de una región STGR para desplegables (id_dl => sigla_dl).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/delegaciones_region_stgr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/delegaciones_region_stgr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region_stgr` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_DelegacionesRegionStgrDataData`):
  - `a_delegaciones` (`array`)

## Casos De Uso

- `src\ubis\application\DelegacionesRegionStgrData`

## Frontend Relacionado

- `frontend/notas/controller/resumen_anual.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.