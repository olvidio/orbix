---
id: "actividadestudios.posibles_asignaturas_ca_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/posibles_asignaturas_ca_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/posibles_asignaturas_ca_data.php"
entrada: ["post.id_activ:integer", "post.nom_activ:string"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_PosiblesAsignaturasCaDataData"
respuesta_data: ["nom_activ:string", "aAsignaturas_alumnos:list<array{nom_asignatura: string, id_asignatura: int, posibles_alumnos: int, aNombresAlumnos: list<string>}>", "a_alumnos_fin_c:list<array{apellidos_nombre: string, asignaturas: mixed}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/posibles_asignaturas_ca.php"]
casos_uso: ["src\\actividadestudios\\application\\PosiblesAsignaturasCaData"]
tags: ["actividadestudios", "posibles", "asignaturas", "ca", "data"]
estado_revision: "revisado"
---

# Posibles Asignaturas Ca Data

Para un CA, calcula qué asignaturas podrían darse (cuántos asistentes las tienen aún pendientes) y
qué alumnos están cerca de terminar el cuadrienio. Respalda `posibles_asignaturas_ca`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre los asistentes de la actividad `id_activ` (solo niveles STGR activos) y, con
`AsignaturasPendientes`, calcula por alumno las asignaturas de cuadrienio que le faltan; los que
tienen menos de 5 pendientes se listan en `a_alumnos_fin_c`. Después, para cada asignatura de
cuadrienio (`id_tipo != 8`), cuenta cuántos alumnos no la tienen aprobada (`posibles_alumnos`) y sus
nombres. `nom_activ` se propaga del token de selección.

## Endpoint

- URL: `/src/actividadestudios/posibles_asignaturas_ca_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/posibles_asignaturas_ca_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | Id de la actividad CA |
| `nom_activ` | `string` | controller+application | No | Nombre de la actividad (se devuelve tal cual) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_PosiblesAsignaturasCaDataData`):
  - `nom_activ` (`string`).
  - `aAsignaturas_alumnos` (`list`): `{nom_asignatura, id_asignatura, posibles_alumnos, aNombresAlumnos}`.
  - `a_alumnos_fin_c` (`list`): `{apellidos_nombre, asignaturas}` (alumnos con < 5 asignaturas pendientes).

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`posibles_asignaturas_ca.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\PosiblesAsignaturasCaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/posibles_asignaturas_ca.php`