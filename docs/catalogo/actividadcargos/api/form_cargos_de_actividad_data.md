---
id: "actividadcargos.form_cargos_de_actividad_data"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/form_cargos_de_actividad_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php"
entrada: ["post.id_dossier:integer", "post.id_nom:integer", "post.id_pau:integer", "post.mod:string", "post.obj_pau:string", "post.permiso:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro a nadie con id_nom: <id>"]
frontend_referencias: ["frontend/actividadcargos/controller/form_cargos_de_actividad.php"]
casos_uso: ["src\\actividadcargos\\application\\FormCargosDeActividadData"]
tags: ["actividadcargos", "form", "cargos", "de", "actividad", "data"]
estado_revision: "revisado"
---

# Form Cargos De Actividad Data

Construye los datos del formulario de cargos de una actividad (vista por actividad). Los
desplegables (persona y cargo) se arman en el front (`FormCargosDeActividadHashCompose::withDesplegablesHtml`)
a partir de `personas_select` / `cargos_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Prepara el alta o la edición de un cargo dentro de una actividad. Distingue tres situaciones a
partir de la entrada:

- **Editar** (`sel` trae `id_item`): carga el `ActividadCargo` existente y precarga cargo, observaciones y `puede_agd`.
- **Alta sobre persona conocida** (`id_dossier = 3101` o `sel` con `id_nom`): fija la persona destino.
- **Alta con selección de persona** (`obj_pau` presente): devuelve `personas_select` con el colectivo indicado (`PersonaN`, `PersonaNax`, `PersonaAgd`, `PersonaS`, `PersonaSSSC`, `PersonaEx`).

Si no se cumple ninguna, responde `{'redir': 'go_atras'}`.

## Endpoint

- URL: `/src/actividadcargos/form_cargos_de_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_nom#id_item#?#id_schema`; si trae `id_item` → modo edición |
| `id_dossier` | `integer` | application | No | Por defecto `3102` (cargos); `3101` fija persona destino |
| `id_nom` | `integer` | application | No | Persona destino cuando no llega por `sel` |
| `id_pau` | `integer` | application | No | Es el `id_activ` (va al hidden `id_activ`) |
| `obj_pau` | `string` | application | No | Colectivo a listar en `personas_select` (`PersonaN`, `PersonaAgd`, …) |
| `mod` | `string` | application | No | `nuevo` activa el checkbox `asis` / `asis_presente` |
| `permiso` | `string` | application | No | Se propaga como hidden |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload del formulario, con claves como: `obj`, `id_nom_real`, `ape_nom`, `observ`,
  `puede_agd`, `chk`, `Qmod`, `Qid_pau`, `Qid_item`, `Qobj_pau`, `Qid_schema`, `Qid_nom`,
  `id_dossier`, `show_person_desplegable`, `show_asis`, `cargos_select`, `hash_form_config`
  (`campos_form` / `campos_no` / `campos_hidden`), `url_cargo_nuevo`, `url_cargo_editar` y,
  solo en alta con selección, `personas_select`.
- En error: `success: false` con el mensaje; en salir sin datos: `data.redir = 'go_atras'`.

## Permisos

- El caso de uso no aplica un control de permisos propio: la autorización de oficina se resuelve
  en el frontend (`form_cargos_de_actividad.php`) y en `$_SESSION['oPerm']`. No inferir permisos
  concretos aquí.

## Casos De Uso

- `src\actividadcargos\application\FormCargosDeActividadData`

## Frontend Relacionado

- `frontend/actividadcargos/controller/form_cargos_de_actividad.php`
- `frontend/actividadcargos/helpers/FormCargosDeActividadHashCompose.php` (monta desplegables + HashB)