---
id: "actividadcargos.form_cargos_personas_en_actividad_data"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/form_cargos_personas_en_actividad_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_personas_en_actividad_data.php"
entrada: ["post.id_dossier:integer", "post.id_pau:integer", "post.id_tipo:integer", "post.mod:string", "post.permiso:integer", "post.que_dl:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no encuentro el cargo", "actividad no encontrada"]
frontend_referencias: ["frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php"]
casos_uso: ["src\\actividadcargos\\application\\FormCargosPersonasEnActividadData"]
tags: ["actividadcargos", "form", "cargos", "personas", "en", "actividad", "data"]
estado_revision: "revisado"
---

# Form Cargos Personas En Actividad Data

Construye los datos del formulario de cargos desde la **vista por persona**: dada una persona,
permite asignarle un cargo en una actividad (o editar uno existente).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Es el simétrico de `form_cargos_de_actividad_data` pero orientado a persona:

- **Editar** (`sel` trae `id_item`): carga el `ActividadCargo`, su actividad, cargo, observaciones y `puede_agd`.
- **Alta**: lista las actividades candidatas (`aActividades`) filtradas por tipo (`id_tipo`, por defecto el sf/sv del usuario), delegación (`que_dl`; si no llega, se excluye la propia con operador `!=`) y estado `ACTUAL`.

## Endpoint

- URL: `/src/actividadcargos/form_cargos_personas_en_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_personas_en_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_item#…`; si trae `id_item` → modo edición |
| `id_pau` | `integer` | application | No | Es el `id_nom` de la persona (va al hidden `id_nom`) |
| `id_dossier` | `integer` | application | No | Por defecto `1302` |
| `id_tipo` | `integer` | application | No | Filtro de tipo de actividad en alta; `0` → sf/sv del usuario |
| `que_dl` | `string` | application | No | Delegación a filtrar; si vacío se excluye la propia |
| `mod` | `string` | application | No | `nuevo` activa `asis` / `asis_presente` |
| `permiso` | `integer` | application | No | Se propaga |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` incluye: `obj`, `Qpermiso`, `id_activ_real`, `nom_activ`, `aActividades` (filas
  `{id_activ, nom_activ}`), `cargos_select`, `hash_form_config`, `chk`, `observ`, `Qmod`,
  `url_cargo_nuevo`, `url_cargo_editar`.
- En error: `success: false` con `no encuentro el cargo` / `actividad no encontrada`.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en frontend y
  `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadcargos\application\FormCargosPersonasEnActividadData`

## Frontend Relacionado

- `frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php`