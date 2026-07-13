---
id: "actividadestudios.form_matriculas_de_una_persona_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/form_matriculas_de_una_persona_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/form_matriculas_de_una_persona_data.php"
entrada: ["post.sel:array", "post.id_nom:integer", "post.id_pau:integer", "post.id_activ:integer", "post.id_asignatura:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_FormMatriculasDeUnaPersonaDataData"
respuesta_data: ["nom_activ:string", "mod:string", "id_asignatura_real:integer", "nombre_corto:string", "chk_preceptor:string", "id_preceptor:string|int", "oDesplProfesores_opciones:array", "oDesplNiveles_opciones:array", "condicion_js:string", "camposForm:string", "a_camposHidden:array"]
requiere_hashb: false
errores: ["No se ha encontrado actividad con id: <id>", "no encuentro la matricula", "No se ha encontrado la asignatura con id: <id>"]
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php"]
casos_uso: ["src\\actividadestudios\\application\\FormMatriculasDeUnaPersonaData"]
tags: ["actividadestudios", "form", "matriculas", "de", "una", "persona", "data"]
estado_revision: "revisado"
---

# Form Matriculas De Una Persona Data

Prepara el formulario de alta/edición de la matrícula de una persona en una asignatura de una
actividad. Respalda `form_matriculas_de_una_persona`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Determina persona (`id_nom`, con fallback a `id_pau`) y actividad/asignatura (de `sel` =
`id_activ#id_asignatura` o de los campos sueltos) y distingue:

- **Editar** (`id_asignatura_real > 0`): carga la `Matricula`, precarga preceptor/`id_preceptor` y,
  si hay preceptor, el desplegable de profesores de la DL.
- **Nuevo**: calcula las asignaturas que le faltan al alumno (no superadas ni ya matriculadas) para
  el desplegable de niveles.

Añade `condicion_js` (ids de asignaturas opcionales genéricas) usado por el front.

## Endpoint

- URL: `/src/actividadestudios/form_matriculas_de_una_persona_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/form_matriculas_de_una_persona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_activ#id_asignatura`; si trae asignatura → modo edición |
| `id_nom` | `integer` | application | No | Persona; si ≤ 0 se usa `id_pau` |
| `id_pau` | `integer` | application | No | Alias de persona (fallback de `id_nom`) |
| `id_activ` | `integer` | application | No | Actividad (si no llega por `sel`) |
| `id_asignatura` | `integer` | application | No | Asignatura (si no llega por `sel`) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_FormMatriculasDeUnaPersonaDataData`):
  - `nom_activ` (`string`), `mod` (`nuevo`/`editar`), `id_asignatura_real` (`integer`).
  - `nombre_corto` (`string`), `chk_preceptor` (`string`), `id_preceptor` (`string|int`).
  - `oDesplProfesores_opciones` (`array`): profesores de la DL (solo si hay preceptor).
  - `oDesplNiveles_opciones` (`array`): asignaturas que le faltan (modo nuevo).
  - `condicion_js` (`string`): condición JS de asignaturas opcionales genéricas.
  - `camposForm` (`string`), `a_camposHidden` (`array`).

## Errores conocidos

- `No se ha encontrado actividad con id: <id>`.
- `no encuentro la matricula` (edición sin matrícula).
- `No se ha encontrado la asignatura con id: <id>`.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_matriculas_de_una_persona.php`) y en `$_SESSION['oPerm']`. No inferir permisos aquí.

## Casos De Uso

- `src\actividadestudios\application\FormMatriculasDeUnaPersonaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`