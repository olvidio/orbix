---
id: "actividadestudios.form_asignaturas_de_una_actividad_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/form_asignaturas_de_una_actividad_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/form_asignaturas_de_una_actividad_data.php"
entrada: ["post.sel:array", "post.pau:string", "post.id_pau:integer", "post.id_activ:integer", "post.id_asignatura:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_FormAsignaturasDeUnaActividadDataData"
respuesta_data: ["mod:string", "id_activ:integer", "id_asignatura:integer", "nombre_corto:string", "chk_avisado:string", "chk_confirmado:string", "chk_preceptor:string", "f_ini:string", "f_fin:string", "oDesplProfesores_opciones:array", "id_profesor_sel:int|string", "oDesplAsignaturas_opciones:array", "primary_key_s:string", "camposForm:string", "a_camposHidden:array"]
requiere_hashb: false
errores: ["no encuentro la asignatura de actividad", "No se ha encontrado la asignatura con id: <id>", "debería haber un nombre de asignatura"]
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\FormAsignaturasDeUnaActividadData"]
tags: ["actividadestudios", "form", "asignaturas", "de", "una", "actividad", "data"]
estado_revision: "revisado"
---

# Form Asignaturas De Una Actividad Data

Prepara el formulario de alta/edición de una asignatura dentro de una actividad
(`ActividadAsignatura`): desplegables de profesor y asignatura, checks de aviso/confirmación y
preceptor, fechas y campos ocultos. Respalda `form_asignaturas_de_una_actividad`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Determina la actividad y asignatura destino (de `sel` = `id_activ#id_asignatura`, o de `id_pau`
cuando `pau='a'`, o de `id_activ`/`id_asignatura` sueltos) y distingue:

- **Editar** (`id_asignatura > 0`): carga la `ActividadAsignatura`, precarga profesor (desplegable de
  profesores de la asignatura), avisos (`avisado`/`confirmado`), preceptor y fechas.
- **Nuevo**: desplegable de profesores de la actividad y desplegable de asignaturas con separador;
  añade `id_asignatura` a `camposForm`.

## Endpoint

- URL: `/src/actividadestudios/form_asignaturas_de_una_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/form_asignaturas_de_una_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_activ#id_asignatura`; si trae `id_asignatura` → modo edición |
| `pau` | `string` | application | No | `a` hace que `id_pau` sea la actividad |
| `id_pau` | `integer` | application | No | Actividad cuando `pau='a'` |
| `id_activ` | `integer` | application | No | Actividad destino (si no llega por `sel`/`id_pau`) |
| `id_asignatura` | `integer` | application | No | Asignatura a editar (0 → alta) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_FormAsignaturasDeUnaActividadDataData`):
  - `mod` (`string`)
  - `id_activ` (`integer`)
  - `id_asignatura` (`integer`)
  - `nombre_corto` (`string`)
  - `chk_avisado` (`string`)
  - `chk_confirmado` (`string`)
  - `chk_preceptor` (`string`)
  - `f_ini` (`string`)
  - `f_fin` (`string`)
  - `oDesplProfesores_opciones` (`array`)
  - `id_profesor_sel` (`int|string`)
  - `oDesplAsignaturas_opciones` (`array`)
  - `primary_key_s` (`string`): clave primaria (`id_activ=… AND id_asignatura=…`) en edición.
  - `camposForm` (`string`): lista de campos del form separada por `!`.
  - `a_camposHidden` (`array`): campos ocultos (`id_activ`, y en edición `id_asignatura`/`primary_key_s`).

## Errores conocidos

- `no encuentro la asignatura de actividad` (edición sin `ActividadAsignatura`).
- `No se ha encontrado la asignatura con id: <id>`.
- `debería haber un nombre de asignatura` (alta sin `id_activ`).

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_asignaturas_de_una_actividad.php`) y en `$_SESSION['oPerm']`. No inferir permisos aquí.

## Casos De Uso

- `src\actividadestudios\application\FormAsignaturasDeUnaActividadData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`