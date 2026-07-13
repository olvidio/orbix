---
id: "casas.calendario_ubi_resumen_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/calendario_ubi_resumen_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php"
entrada: ["post.G:integer", "post.id_ubi:integer", "post.inc_t:integer", "post.seccion:string"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Casa no encontrada"]
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/casas/controller/calendario_ubi_resumen_body.php"]
casos_uso: ["src\\casas\\application\\CalendarioUbiResumenData"]
tags: ["casas", "calendario", "ubi", "resumen", "data"]
estado_revision: "revisado"
---

# Calendario Ubi Resumen Data

Estudio económico y de ocupación de una casa para previsión del calendario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de `apps/casas/controller/calendario_ubi_resumen_ajax.php`. Calcula tarifas actual/prevista,
días de ocupación sv/sf, gastos del año anterior, previsión de ingresos (`p_ip`) y desglose por
actividades del año próximo. Parámetros `seccion` (`sv`/`sf`), `G` (% crecimiento ingresos), `inc_t`
(% incremento tarifas). Si no hay gastos del año anterior (`r_it <= 0`) devuelve `ok: false`,
`error: sin_gastos_anterior` con datos parciales.

## Endpoint

- URL: `/src/casas/calendario_ubi_resumen_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller+application | Sí | Casa |
| `seccion` | `string` | controller+application | No | `sv` o `sf` |
| `G` | `integer` | controller+application | No | % crecimiento (default 0) |
| `inc_t` | `integer` | controller+application | No | % incremento tarifas previstas |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- Payload extenso: `ok`, `error`, `nombre_ubi`, `plazas_min`, `a_tarifas_actual`, `a_tarifas_prev`,
  `p_dv`/`p_df`, `r_it`/`r_idl`, `p_ip`, `a_actividades[]`, totales `p_tda`, `p_ta`, `p_tia`,
  `dias_libres`, `dif_asistencias`, `dif_ingresos`, `inc_p`/`inc_d`/`inc_pt`, etc.

## Errores conocidos

- `Casa no encontrada` (`ok: false`)
- `sin_gastos_anterior` (código en `error`, sin gastos del año anterior)

## Permisos

- Sin control propio; acceso vía menú de calendario / estadísticas económicas.

## Casos De Uso

- `src\casas\application\CalendarioUbiResumenData`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php` (shell) y
  `calendario_ubi_resumen_body.php` (cuerpo AJAX por casa/sección).
