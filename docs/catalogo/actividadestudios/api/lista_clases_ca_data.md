---
id: "actividadestudios.lista_clases_ca_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/lista_clases_ca_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/lista_clases_ca_data.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_ListaClasesCaDataData"
respuesta_data: ["msg_err:string", "nom_activ:string", "nom_director_est:string", "datos_asignatura:array"]
requiere_hashb: false
errores: ["No se ha encontrado la asignatura con id: <id>"]
frontend_referencias: ["frontend/actividadestudios/controller/lista_clases_ca.php"]
casos_uso: ["src\\actividadestudios\\application\\ListaClasesCaData"]
tags: ["actividadestudios", "lista", "clases", "ca", "data"]
estado_revision: "revisado"
---

# Lista Clases Ca Data

Lista de clases de un CA: por cada asignatura, su profesor/preceptor y los alumnos matriculados.
Respalda `lista_clases_ca` (vista imprimible).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para la actividad `id_activ` resuelve el director de estudios (cargo `d.est.`) y, por cada
`ActividadAsignatura`, arma una fila con el profesor (o preceptor, según `tipo`), el nombre corto de
la asignatura y la lista de alumnos matriculados (nombre → centro), ordenados sin acentos. Si la
actividad no existe, devuelve payload vacío con `msg_err`.

## Endpoint

- URL: `/src/actividadestudios/lista_clases_ca_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/lista_clases_ca_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | Id de la actividad CA |

El controller lee `id_activ` y llama a `execute(['id_activ' => …])`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_ListaClasesCaDataData`):
  - `msg_err` (`string`): avisos (actividad/persona no encontrada).
  - `nom_activ` (`string`): nombre de la actividad.
  - `nom_director_est` (`string`): nombre del director de estudios o aviso.
  - `datos_asignatura` (`array`): filas `{nom_profesor, tipo_profesor, nombre_corto, alumnos}`
    (`alumnos` = mapa `nombre => centro`).

## Errores conocidos

- `No se ha encontrado la asignatura con id: <id>` (excepción si falta una asignatura referenciada).

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`lista_clases_ca.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\ListaClasesCaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/lista_clases_ca.php`