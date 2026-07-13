---
id: "actividadplazas.gestion_plazas_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/gestion_plazas_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_tipo_activ:string", "post.periodo:string", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_GestionPlazasDataData"
respuesta_data: ["a_cabeceras:list<array<string, mixed>>", "a_valores:array", "a_grupo:array", "extendida:boolean", "id_tipo_activ:string", "sactividad:string", "year:int|string", "periodo:string", "empiezamin:string", "empiezamax:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/gestion_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\GestionPlazasData"]
tags: ["actividadplazas", "gestion", "plazas", "data"]
estado_revision: "revisado"
---

# Gestion Plazas Data

Data builder de la pantalla principal `gestion_plazas`: devuelve el cuadro (cabeceras + filas
por actividad y dl del grupo de estudios de mi dl) con las plazas totales, concedidas y pedidas,
para que el controller frontend monte la `frontend\shared\web\TablaEditable`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Calcula el cuadro de plazas del periodo seleccionado:

- Resuelve `id_tipo_activ` a partir de `sasistentes` + `sactividad`/`sactividad2` (o lo toma directo
  si ya viene), y el periodo (`year` + `periodo` o rango `empiezamin`/`empiezamax`).
- Localiza las delegaciones del mismo `grupo_estudios` que mi dl y, por cada una, las actividades
  publicadas en estado actual dentro del periodo.
- Para cada actividad calcula `tot` (plazas totales, editable solo si la actividad es de mi dl) y,
  por cada dl del grupo, las columnas `<dl>-c` (concedidas) y `<dl>-p` (pedidas) con su flag
  `editable` según quién organiza y cuál es mi dl.
- Si no hay delegaciones en el grupo devuelve el cuadro vacío (arrays vacíos) conservando los
  metadatos de filtro.

## Endpoint

- URL: `/src/actividadplazas/gestion_plazas_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sasistentes` | `string` | controller | No | Colectivo (`n`, `agd`, `s`…); junto a `sactividad` resuelve `id_tipo_activ` |
| `sactividad` | `string` | controller | No | Tipo (`ca`, `cv`, `crt`, `cve`…); si vacío se deriva de `id_tipo_activ` |
| `sactividad2` | `string` | controller | No | Actividad de 2 dígitos (semestres); si viene, activa `extendida` |
| `id_tipo_activ` | `string` | controller | No | Si llega, tiene prioridad sobre `sasistentes`/`sactividad` |
| `year` | `string` | controller | No | Año del periodo; por defecto el año actual |
| `periodo` | `string` | controller | No | Clave de periodo (`curso_ca`, `curso_crt`, `trimestre_*`…) |
| `empiezamin` | `string` | controller | No | Inicio de rango manual; con `empiezamax` fuerza periodo `otro` |
| `empiezamax` | `string` | controller | No | Fin de rango manual |

El controller normaliza cada campo con `FuncTablasSupport::inputString` antes de invocar el caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadplazas_GestionPlazasDataData`):
  - `a_cabeceras` (`list<array<string, mixed>>`): id (oculta), actividad, org, total y, por cada dl, `<dl>-c` / `<dl>-p`.
  - `a_valores` (`array`): filas por actividad con `id`, `actividad`, `dlorg`, `tot` y las celdas `<dl>-c`/`<dl>-p` (cada una `{editable, valor}`).
  - `a_grupo` (`array`): mapa `dl => id_dl` de las delegaciones del grupo.
  - `extendida` (`boolean`): true si se filtró por `sactividad2`.
  - `id_tipo_activ` (`string`), `sactividad` (`string`), `year` (`int|string`), `periodo` (`string`), `empiezamin` (`string`), `empiezamax` (`string`): eco de los filtros resueltos.

## Permisos

- El caso de uso no aplica un control de permisos propio: la edición por celda se decide con los flags
  `editable` calculados a partir de `mi_delef()`/`mi_region()` (`ConfigGlobal`) y la autorización de
  oficina se resuelve en frontend + `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadplazas\application\GestionPlazasData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/gestion_plazas.php`
