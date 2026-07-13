---
id: "asistentes.form_actividades_de_una_persona_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/form_actividades_de_una_persona_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/form_actividades_de_una_persona_data.php"
entrada: ["post.id_pau:integer", "post.id_tipo:string", "post.obj_pau:string", "post.que_dl:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha encontrado la actividad con id: %s", "No se ha encontrado el asistente (id_nom: %s, id_activ: %s)", "los datos de asistencia los modifica el propietario de la plaza: %s"]
frontend_referencias: ["frontend/asistentes/controller/form_actividades_de_una_persona.php"]
casos_uso: ["src\\asistentes\\application\\FormActividadesDeUnaPersonaData"]
tags: ["asistentes", "form", "actividades", "persona", "data"]
estado_revision: "revisado"
---

# Form Actividades De Una Persona Data

Dossier actividades de una persona (1301): datos del formulario alta/edición de asistencia.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Sin `sel` → modo `nuevo`: desplegable de actividades del tipo (`id_tipo` o `mi_sfsv`), filtradas por `que_dl`.
- Con `sel` (`id_activ#...`) → modo `editar`: carga asistente y nombre de actividad.
- Campos de plaza/propietario si `actividadplazas` instalado.

## Endpoint

- URL: `/src/asistentes/form_actividades_de_una_persona_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/form_actividades_de_una_persona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_pau` | `integer` | application | Si | `id_nom` de la persona |
| `obj_pau` | `string` | application | No | Clase persona (`PersonaN`, `PersonaEx`, …) |
| `id_tipo` | `string` | application | No | Prefijo tipo actividad en alta; default `mi_sfsv` |
| `que_dl` | `string` | application | No | Filtra `dl_org`; vacío excluye delegación actual |
| `sel` | `array` | application | No | Token `id_activ#...` para edición |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data` (o `error`):
  - `obj`, `id_nom`, `id_activ_real`, `nom_activ`, checkboxes, `observ`
  - `plazas_installed`, `plaza_*`, `propietario_*`
  - `hash_main` (`pau=p`, `mod`, hidden `id_nom`/`id_activ`)
  - `paths`, `actividades_opciones` (alta), `ajax_propietarios`

## Errores conocidos

- `No se ha encontrado la actividad con id: %s`
- `No se ha encontrado el asistente (id_nom: %s, id_activ: %s)`
- `los datos de asistencia los modifica el propietario de la plaza: %s`

## Permisos

- Bloqueo por propietario de plaza en edición.
- Dossier persona: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\FormActividadesDeUnaPersonaData`

## Frontend Relacionado

- `frontend/asistentes/controller/form_actividades_de_una_persona.php` +
  `FormActividadesDeUnaPersonaRender`.
