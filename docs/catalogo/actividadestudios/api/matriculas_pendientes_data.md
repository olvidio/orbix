---
id: "actividadestudios.matriculas_pendientes_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matriculas_pendientes_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matriculas_pendientes_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_MatriculasPendientesDataData"
respuesta_data: ["msg_err:string", "aviso:string", "a_valores:array"]
requiere_hashb: false
errores: ["No se ha encontrado la asignatura con id: <id>"]
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_pendientes.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasPendientesData"]
tags: ["actividadestudios", "matriculas", "pendientes", "data"]
estado_revision: "revisado"
---

# Matriculas Pendientes Data

Filas de matrículas pendientes de nota (asignaturas sin acta) para
`frontend/actividadestudios/controller/matriculas_pendientes.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recupera las matrículas pendientes (`getMatriculasPendientes`) y arma una fila por matrícula con
alumno, actividad, asignatura y marca de preceptor. Resuelve cada persona con `PersonaListadoLookup`
acumulando avisos de región STGR. Ordena por nombre de alumno.

## Endpoint

- URL: `/src/actividadestudios/matriculas_pendientes_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_pendientes_data.php`

## Entrada

Sin parámetros POST: `execute()` no recibe argumentos (lee el conjunto de matrículas pendientes del repositorio).

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_MatriculasPendientesDataData`):
  - `msg_err` (`string`): avisos de alumnos/actividades no encontrados.
  - `aviso` (`string`): aviso de personas de región STGR sin región (`RegionStgrAviso::formatear`).
  - `a_valores` (`array`): filas con `sel` = `id_activ#id_asignatura#id_nom` y columnas `1` nombre
    actividad, `2` nombre corto asignatura, `3` alumno, `4` marca preceptor (`x`).

## Efectos colaterales

- Si una matrícula referencia una persona inexistente, la elimina (`matriculaDlRepository->Eliminar`).

## Errores conocidos

- `No se ha encontrado la asignatura con id: <id>` (excepción si falta la asignatura de una matrícula).

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`matriculas_pendientes.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\MatriculasPendientesData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_pendientes.php`