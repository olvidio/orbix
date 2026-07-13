---
id: "asistentes.lista_ultima_activ_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_ultima_activ_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_ultima_activ_data.php"
entrada: ["post.curso:string", "post.id_ubi:string", "post.que:string"]
entrada_obligatoria: ["que"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["alert_html:string", "titulo:string", "stats_html:string", "tabla_html:string"]
requiere_hashb: false
errores: ["No sé en que tipo de actividad hay que mirar las asistencias"]
frontend_referencias: ["frontend/asistentes/controller/lista_ultima_activ.php"]
casos_uso: ["src\\asistentes\\application\\ListaUltimaActivData"]
tags: ["asistentes", "lista", "ultima", "activ", "data"]
estado_revision: "revisado"
---

# Lista Ultima Activ Data

Seguimiento de personas `s` sin asistencia reciente a crt/cv.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según `que` (`crt_s_sg`, `crt_s`, `crt_cel`, `cv_s`, `cv_s_ad`, `cv_jovenes`) filtra personas `s`
activas y muestra su última asistencia al tipo de actividad indicado. Excluye quienes ya asistieron
en el rango de curso calculado. Opcionalmente filtra por `id_ubi` (centro; `999` = todos).

## Endpoint

- URL: `/src/asistentes/lista_ultima_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_ultima_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | application | Si | Tipo de informe (ver Objetivo) |
| `curso` | `string` | application | No | `actual`, `anterior` o vacío |
| `id_ubi` | `string` | application | No | Centro; vacío/`999` = todos |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `alert_html`, `titulo`, `stats_html`, `tabla_html`

## Errores conocidos

- `exit` con `No sé en que tipo de actividad hay que mirar las asistencias` si `que` no reconocido.

## Permisos

- Sin control propio; informes de menú vsg: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\ListaUltimaActivData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_ultima_activ.php` (tras selector `lista_ultim_que_ctr`).
