---
id: "asistentes.lista_est_ctr_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_est_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_est_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_ubi:integer", "post.n_agd:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["lista_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_est_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaEstCtrData"]
tags: ["asistentes", "lista", "est", "ctr", "data"]
estado_revision: "revisado"
---

# Lista Est Ctr Data

Estudios (asignaturas matriculadas) por centro y actividad ca/cv.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada persona de cada centro, lista actividades ca/cv con asistencia propia en el periodo y
desglosa asignaturas matriculadas (o «plan de formación»/«repaso» según nivel stgr). Actividades ya
iniciadas muestran «ya lo ha hecho».

## Endpoint

- URL: `/src/asistentes/lista_est_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_est_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `n_agd` | `string` | application | No | Tipo centro/persona (`n`, `a`, `nm`, `nj`, `sssc`, `c`) |
| `id_ubi` | `integer` | application | No | Si `n_agd=c` |
| `year`, `periodo`, `empiezamin`, `empiezamax` | string | application | No | Rango vía `Periodo` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `lista_html` (`string`): tabla paginada generada por `Lista::listaPaginada()`

## Errores conocidos

- Excepción si asignatura no encontrada: `No se ha encontrado la asignatura con id: %s`

## Permisos

- Sin control propio; listado de menú: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\ListaEstCtrData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_est_ctr.php`.
