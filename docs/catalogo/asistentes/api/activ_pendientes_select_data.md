---
id: "asistentes.activ_pendientes_select_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/activ_pendientes_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/activ_pendientes_select_data.php"
entrada: ["post.any:integer", "post.sactividad:string", "post.tipo_personas:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/activ_pendientes_select.php"]
casos_uso: ["src\\asistentes\\application\\ActivPendientesSelectData"]
tags: ["asistentes", "activ", "pendientes", "select", "data"]
estado_revision: "revisado"
---

# Activ Pendientes Select Data

Listado de personas sin asistencia propia a ca/crt en un curso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Filtra por `tipo_personas` (`n`, `agd`, `sacd`), `sactividad` (`ca`/`crt`) y año de curso. Devuelve
dos tablas: personas de la delegación sin asistencia y personas de otras delegaciones. Enlaces a
`home_persona` vía `link_spec` (sin firmar; firma en `ActivPendientesSelectRender`).

## Endpoint

- URL: `/src/asistentes/activ_pendientes_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/activ_pendientes_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo_personas` | `string` | application | No | `n`, `agd`, `sacd` |
| `sactividad` | `string` | application | No | `ca` o `crt` |
| `any` | `integer` | application | No | Año fin de curso; default año actual |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `paths.form_action`, `hash_main.campos_form`
  - Selectores: `chk_n`, `chk_agd`, `chk_sacd`, `chk_ca`, `chk_crt`, `chk_any_*`, `txt_curso_*`
  - `titulo`
  - `a_cabeceras_activ_pendientes`, `a_valores_activ_pendientes_dl`, `a_valores_activ_pendientes_otras`

## Permisos

- Sin control propio; listado de menú: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\ActivPendientesSelectData`

## Frontend Relacionado

- `frontend/asistentes/controller/activ_pendientes_select.php` +
  `ActivPendientesSelectRender`.
