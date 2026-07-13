---
id: "asistentes.que_ctr_lista_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/que_ctr_lista_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/que_ctr_lista_data.php"
entrada: ["post.id_ubi:integer", "post.lista:string", "post.n_agd:string", "post.periodo:string", "post.sactividad:string", "post.sasistentes:string", "post.ssfsv:string", "post.tipo:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/que_ctr_lista.php"]
casos_uso: ["src\\asistentes\\application\\QueCtrListaData"]
tags: ["asistentes", "que", "ctr", "lista", "data"]
estado_revision: "revisado"
---

# Que Ctr Lista Data

Formulario de filtro por centro/tipo persona/periodo antes del listado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según `lista` (`list_activ`, `list_est`, `profesion`, `ctrex`) monta título, action destino y radios
`n_agd`. Para `list_activ`/`list_est` incluye bloque periodo (`curso_ca`, `curso_crt`, `tot_any`…)
y desplegable de centros. El submit va a `lista_activ_ctr` o `lista_est_ctr`.

## Endpoint

- URL: `/src/asistentes/que_ctr_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/que_ctr_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `lista` | `string` | application | No | `list_activ`, `list_est`, … |
| `ssfsv`, `sasistentes`, `sactividad`, `tipo` | `string` | application | No | Contexto de menú (hidden) |
| `n_agd` | `string` | application | No | Tipo persona/centro seleccionado |
| `id_ubi` | `integer` | application | No | Centro preseleccionado |
| `year`, `periodo` | mixed | application | No | Periodo preseleccionado |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `tituloGros`, `titulo`, `action`, `nomUbi`
  - Radios: `n`, `nj`, `nm`, `a`, `sssc`, `nax`, `c`
  - `opciones_centros`, `id_ubi_sel`, `periodo_form` (si list_activ/list_est)
  - `hash_main`, `locale_us`, `mi_sfsv`

## Permisos

- Sin control propio; listados de menú ACTIVIDADES: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\QueCtrListaData`

## Frontend Relacionado

- `frontend/asistentes/controller/que_ctr_lista.php` + `QueCtrListaRender`.
