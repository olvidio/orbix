---
id: "casas.casas_resumen_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casas_resumen_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/casas_resumen_data.php"
entrada: ["post.cdc_sel:integer", "post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.que:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casas_resumen_lista.php", "frontend/casas/controller/casas_resumen.php"]
casos_uso: ["src\\casas\\application\\CasasResumenData"]
tags: ["casas", "resumen", "data"]
estado_revision: "revisado"
---

# Casas Resumen Data

Resumen económico agregado de casas (ocupación, asistentes, ingresos, gastos, superávit).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de `apps/casas/controller/casas_resumen_ajax.php`. Dos modos según `que`:
- `que=''`: un periodo (año/trimestre/rango) → `modo: periodo`, `a_resumen`, `tot`, `avisos`.
- `que!=''`: estadística anual (6 años desde el próximo) → `modo: anual`, `a_anys`, `a_resumen[id_ubi][any]`, `tot[any]`.

Filtra casas con `cdc_sel` (1=sv+sf, 2=sf, 3=cdcdl, 4=sv, 5=sf, 6=sf+centros ellas, 9=selección manual
`id_cdc`). Aplica ajuste de superávit padre-hijo cuando hay `GrupoCasa`.

## Endpoint

- URL: `/src/casas/casas_resumen_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casas_resumen_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | controller+application | No | Vacío = periodo único; otro valor = modo anual |
| `cdc_sel` | `integer` | controller+application | No | Filtro de conjunto de casas |
| `id_cdc` | `array` | controller+application | No | Con `cdc_sel=9` |
| `periodo` / `year` / `empiezamin` / `empiezamax` | `string` | controller+application | No | Solo modo periodo |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- Cada fila de casa: secciones `0` (nombre, detalles actividad, gasto total), `1`/`2` (sv/sf con
  `dias`, `asist_prev`, `asist`, `in_prev_acu`, `in_acu`, `gasto`, `aportacion`, `superavit` y %),
  más `_id_ubi_padre` / `_id_ubi_hijo`.
- `avisos`: mensajes de datos incompletos por actividad.

## Permisos

- Sin control propio en el caso de uso; el frontend filtra acceso por oficina.

## Casos De Uso

- `src\casas\application\CasasResumenData`

## Frontend Relacionado

- `frontend/casas/controller/casas_resumen_lista.php` y `casas_resumen.php`.
