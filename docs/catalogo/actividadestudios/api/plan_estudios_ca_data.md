---
id: "actividadestudios.plan_estudios_ca_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/plan_estudios_ca_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/plan_estudios_ca_data.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_PlanEstudiosCaDataData"
respuesta_data: ["msg_err:string", "nom_activ:string", "nom_director_est:string", "aPreceptores:array", "aProfesores:array", "aAlumnos:array"]
requiere_hashb: false
errores: ["No se ha encontrado la asignatura con id: <id>"]
frontend_referencias: ["frontend/actividadestudios/controller/plan_estudios_ca.php"]
casos_uso: ["src\\actividadestudios\\application\\PlanEstudiosCaData"]
tags: ["actividadestudios", "plan", "estudios", "ca", "data"]
estado_revision: "revisado"
---

# Plan Estudios Ca Data

Datos del plan de estudios de un CA (centro de estudios): director de estudios, profesores,
preceptores y alumnos con sus asignaturas. Respalda `plan_estudios_ca`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para la actividad `id_activ`:

- Resuelve el director de estudios (cargo `d.est.` en `ActividadCargo`) o el aviso de que hay que
  nombrarlo en el dossier de cargos.
- Recorre las `ActividadAsignatura` y las reparte en `aProfesores` / `aPreceptores` (según `tipo`),
  con nombre corto, créditos y nombre del profesor.
- Recorre los asistentes propios y, por cada alumno, lista sus matrículas (asignatura + créditos +
  marca preceptor) o, si no tiene matrícula, el texto de su nivel (`repaso` / `plan de formación`).

Si la actividad no existe, devuelve el payload vacío con `msg_err`.

## Endpoint

- URL: `/src/actividadestudios/plan_estudios_ca_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/plan_estudios_ca_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | Id de la actividad CA a mostrar |

El controller lee `id_activ` y llama a `execute(['id_activ' => …])`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_PlanEstudiosCaDataData`):
  - `msg_err` (`string`): avisos (actividad/persona no encontrada).
  - `nom_activ` (`string`): nombre de la actividad.
  - `nom_director_est` (`string`): nombre del director de estudios o aviso.
  - `aPreceptores` (`array`): `{nombre_corto, creditos, nom_profesor}` de asignaturas tipo `p`.
  - `aProfesores` (`array`): idem para el resto de asignaturas.
  - `aAlumnos` (`array`): `{nom_persona, ctr, observ_est, aAsignaturas}`.

## Errores conocidos

- `No se ha encontrado la asignatura con id: <id>` (excepción si falta una asignatura referenciada).

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`plan_estudios_ca.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\PlanEstudiosCaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/plan_estudios_ca.php`