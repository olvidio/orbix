---
id: "actividadestudios.matriculas_lista_otras_r_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matriculas_lista_otras_r_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_otras_r_data.php"
entrada: ["post.apellido1:string", "post.esquema_region_stgr:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_MatriculasListaOtrasRDataData"
respuesta_data: ["titulo:string", "titulo_busqueda_por_apellidos:string", "msg_err:string", "aviso:string", "a_valores:array"]
requiere_hashb: false
errores: ["No se pudo resolver el repositorio de notas de otras regiones", "No se pudo determinar el esquema región STGR de la sesión."]
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_lista_otras_r.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasListaOtrasRData"]
tags: ["actividadestudios", "matriculas", "lista", "otras", "r", "data"]
estado_revision: "revisado"
---

# Matriculas Lista Otras R Data

Lista de alumnos de otras regiones STGR pendientes de generar certificado (o búsqueda por apellido).
Respalda `matriculas_lista_otras_r`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dos modos según la entrada:

- **Búsqueda por apellido** (`apellido1` presente): lista personas activas cuyo primer apellido
  empieza por el texto (sin acentos), ordenadas por DL/STGR/apellido.
- **Pendientes de certificado** (sin `apellido1`): recupera del esquema de la región STGR las notas
  de otras regiones sin `json_certificados`, agrupa por alumno y concatena las asignaturas
  (con `(actividad)` y marca `!` si el acta no tiene PDF, `⚠` si la persona no tiene región).

Ordena por nombre de alumno y acumula avisos de región STGR.

## Endpoint

- URL: `/src/actividadestudios/matriculas_lista_otras_r_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_otras_r_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `apellido1` | `string` | application | No | Si llega, activa el modo búsqueda por apellido |
| `esquema_region_stgr` | `string` | application | No | Esquema SV/SF de la región STGR; si vacío se deduce de la sesión |

> Nota histórica (linaje `apps/`): el legacy enviaba `esquema` en POST. En la versión actual el
> frontend **ya no** lo envía; `esquema_region_stgr` se deduce de la delegación/sesión.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_MatriculasListaOtrasRDataData`):
  - `titulo` (`string`): título del modo pendientes (vacío en modo búsqueda).
  - `titulo_busqueda_por_apellidos` (`string`): etiqueta `búsqueda por apellidos`.
  - `msg_err` (`string`): avisos de personas no encontradas.
  - `aviso` (`string`): aviso de región STGR (`RegionStgrAviso::formatear`).
  - `a_valores` (`array`): filas con `sel` = `id_nom` y columnas `1` alumno, `2` DL, `3` alerta
    (`⚠`/`!`), `4` asignaturas, `5` `id_nom`.

## Errores conocidos

- `No se pudo resolver el repositorio de notas de otras regiones`.
- `No se pudo determinar el esquema región STGR de la sesión.`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`matriculas_lista_otras_r.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\MatriculasListaOtrasRData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_lista_otras_r.php`