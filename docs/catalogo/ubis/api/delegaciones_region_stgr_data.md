---
id: "ubis.delegaciones_region_stgr_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/delegaciones_region_stgr_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/delegaciones_region_stgr_data.php"
entrada: ["post.region_stgr:string"]
entrada_obligatoria: ["region_stgr"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DelegacionesRegionStgrDataData"
respuesta_data: ["a_delegaciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/resumen_anual.php"]
casos_uso: ["src\\ubis\\application\\DelegacionesRegionStgrData"]
tags: ["ubis", "delegaciones", "region", "stgr", "data"]
estado_revision: "revisado"
errores: ["Se requiere region_stgr"]
---

# Delegaciones Region Stgr Data

Lista delegaciones de una región STGR para desplegables dependientes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista delegaciones de una región STGR para desplegables dependientes.

## Endpoint

- URL: `/src/ubis/delegaciones_region_stgr_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/delegaciones_region_stgr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region_stgr` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_delegaciones`: map id_dl=>sigla_dl de la región STGR

## Errores conocidos
- `Se requiere region_stgr`

## Permisos

Desplegable compartido sin pantalla ubis directa.

## Casos De Uso

- `src\ubis\application\DelegacionesRegionStgrData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/notas/controller/resumen_anual.php"]`).
