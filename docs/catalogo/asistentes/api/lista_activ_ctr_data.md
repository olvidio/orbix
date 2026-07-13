---
id: "asistentes.lista_activ_ctr_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_activ_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_activ_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_ubi:integer", "post.n_agd:string", "post.periodo:string", "post.sactividad:string", "post.sasistentes:string", "post.ssfsv:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaActivCtrDataData"
respuesta_data: ["aCentros:array"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_activ_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaActivCtrData"]
tags: ["asistentes", "lista", "activ", "ctr", "data"]
estado_revision: "revisado"
---

# Lista Activ Ctr Data

Asistentes a actividades agrupados por centro.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tras elegir centro/tipo persona/periodo en `que_ctr_lista`, lista por cada centro las personas activas
y sus actividades con asistencia propia en el rango de fechas. Marca plazas pendientes con alerta HTML
si `plaza < ASIGNADA`. Incluye tipos extra (agd cv, sr crt) según colectivo.

## Endpoint

- URL: `/src/asistentes/lista_activ_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_activ_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ssfsv` | `string` | application | No | Puede forzarse a `sf` si permisos `vcsd`/`des` y `mi_sfsv=1` |
| `sasistentes` | `string` | application | No | Colectivo asistentes |
| `sactividad` | `string` | application | No | `ca`, `crt`, `cv`, … |
| `n_agd` | `string` | application | No | `n`, `a`, `nm`, `nj`, `sssc`, `c` (centro concreto) |
| `id_ubi` | `integer` | application | No | Obligatorio si `n_agd=c` |
| `year`, `periodo`, `empiezamin`, `empiezamax` | mixed | application | No | Filtro `Periodo` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `aCentros` (`object`): `id_ubi` → `{nombre_ubi, personas: [{ap_nom, actividades: string[]}]}`

## Permisos

- Sin control propio en el caso de uso; `ssfsv` se ajusta con `$_SESSION['oPerm']` solo para filtrar datos.

## Casos De Uso

- `src\asistentes\application\ListaActivCtrData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_activ_ctr.php` (destino del form `que_ctr_lista`).
