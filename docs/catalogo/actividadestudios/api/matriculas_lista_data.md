---
id: "actividadestudios.matriculas_lista_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matriculas_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_data.php"
entrada: ["post.inicioIso:string", "post.finIso:string"]
entrada_obligatoria: ["inicioIso", "finIso"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_MatriculasListaDataData"
respuesta_data: ["titulo:string", "msg_err:string", "a_valores:array"]
requiere_hashb: false
errores: ["Se requieren inicioIso y finIso", "No se ha encontrado la asignatura con id: <id>"]
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_lista.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasListaData"]
tags: ["actividadestudios", "matriculas", "lista", "data"]
estado_revision: "revisado"
---

# Matriculas Lista Data

Listado de matrículas en un intervalo de fechas (una fila por matrícula de actividades cuyo `f_ini`
cae en el periodo). Usado por `matriculas_lista` vía PostRequest.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el listado de matrículas realizadas en el intervalo `[inicioIso, finIso]`. Selecciona las
actividades cuyo `f_ini` cae `BETWEEN` esas fechas, recupera sus matrículas (`MatriculaDl`) y, por
cada una, resuelve alumno (apellidos + e-mails), centro, DL, nombre de actividad, nombre corto de la
asignatura, preceptor (nombre + e-mails si es preceptor) y la nota (`nota [nota_max]`). Ordena por
nombre de alumno y asignatura. Los errores no bloqueantes (persona/actividad no encontrada) se
acumulan en `msg_err`.

## Endpoint

- URL: `/src/actividadestudios/matriculas_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `inicioIso` | `string` | controller | Sí | Fecha ISO inicio; el controller la remapea a `inicio_iso` para el caso de uso |
| `finIso` | `string` | controller | Sí | Fecha ISO fin; el controller la remapea a `fin_iso` |

El controller valida que ambas lleguen (si no, lanza el error) y llama a `execute(['inicio_iso' => …, 'fin_iso' => …])`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_MatriculasListaDataData`):
  - `titulo` (`string`): `Lista de matrículas en el periodo: <inicio> - <fin>.`
  - `msg_err` (`string`): avisos acumulados (alumno/actividad no encontrados).
  - `a_valores` (`array`): filas indexadas; cada fila con `sel` = `id_activ#id_asignatura#id_nom` y
    columnas `1` alumno (+e-mails), `2` centro, `3` DL, `4` nombre actividad, `5` nombre corto
    asignatura, `6` preceptor, `7` nota (`nota [nota_max]`).

## Errores conocidos

- `Se requieren inicioIso y finIso` (controller, si falta alguna fecha).
- `No se ha encontrado la asignatura con id: <id>` (excepción si la asignatura de una matrícula no existe).

## Permisos

- El caso de uso no aplica control de permisos propio: la autorización de oficina se resuelve en el
  frontend (`matriculas_lista.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\MatriculasListaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_lista.php`