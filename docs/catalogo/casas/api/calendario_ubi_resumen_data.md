---
id: "casas.calendario_ubi_resumen_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/calendario_ubi_resumen_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php"
entrada: ["post.G:integer", "post.id_ubi:integer", "post.inc_t:integer", "post.seccion:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_CalendarioUbiResumenDataData"
respuesta_data: ["ok:boolean", "error:string", "any_anterior:integer", "any_actual:integer", "any_prev:integer", "id_ubi:integer", "seccion:string", "nombre_ubi:string", "plazas_min:integer", "G:integer", "inc_t:integer", "p_dv:integer", "p_df:integer", "a_tarifas_actual:array", "a_tarifas_prev:array", "r_it:number", "r_idl:number", "r_idef:number", "r_ip:number", "r_ta:integer", "r_tia:number", "p_ip:number", "p_ip_txt:string", "p_ta_min:integer", "p_ta_min_txt:string", "p_dseccion:integer", "total_txt:string", "a_actividades:array", "p_tac:integer", "p_tda:number", "p_tap:integer", "p_ta:int|float", "p_tia:number", "p_tarifa:number", "p_ti_min:number", "dias_libres:int|float", "dif_asistencias:number", "dif_ingresos:number", "inc_p:int|string", "inc_d:int|float", "inc_pt:int|float"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/casas/controller/calendario_ubi_resumen_body.php"]
casos_uso: ["src\\casas\\application\\CalendarioUbiResumenData"]
tags: ["casas", "calendario", "ubi", "resumen", "data"]
estado_revision: "generado"
---

# Calendario Ubi Resumen Data

Endpoint backend: datos del estudio económico de una casa (`calendario_ubi_resumen`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/calendario_ubi_resumen_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `G` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `inc_t` | `integer` | controller+application | No | controller+application |
| `seccion` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `casas_CalendarioUbiResumenDataData`):
  - `ok` (`boolean`)
  - `error` (`string`)
  - `any_anterior` (`integer`)
  - `any_actual` (`integer`)
  - `any_prev` (`integer`)
  - `id_ubi` (`integer`)
  - `seccion` (`string`)
  - `nombre_ubi` (`string`)
  - `plazas_min` (`integer`)
  - `G` (`integer`)
  - `inc_t` (`integer`)
  - `p_dv` (`integer`)
  - `p_df` (`integer`)
  - `a_tarifas_actual` (`array`)
  - `a_tarifas_prev` (`array`)
  - `r_it` (`number`)
  - `r_idl` (`number`)
  - `r_idef` (`number`)
  - `r_ip` (`number`)
  - `r_ta` (`integer`)
  - `r_tia` (`number`)
  - `p_ip` (`number`)
  - `p_ip_txt` (`string`)
  - `p_ta_min` (`integer`)
  - `p_ta_min_txt` (`string`)
  - `p_dseccion` (`integer`)
  - `total_txt` (`string`)
  - `a_actividades` (`array`)
  - `p_tac` (`integer`)
  - `p_tda` (`number`)
  - `p_tap` (`integer`)
  - `p_ta` (`int|float`)
  - `p_tia` (`number`)
  - `p_tarifa` (`number`)
  - `p_ti_min` (`number`)
  - `dias_libres` (`int|float`)
  - `dif_asistencias` (`number`)
  - `dif_ingresos` (`number`)
  - `inc_p` (`int|string`)
  - `inc_d` (`int|float`)
  - `inc_pt` (`int|float`)

## Casos De Uso

- `src\casas\application\CalendarioUbiResumenData`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php`
- `frontend/casas/controller/calendario_ubi_resumen_body.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.