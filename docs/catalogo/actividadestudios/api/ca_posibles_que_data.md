---
id: "actividadestudios.ca_posibles_que_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/ca_posibles_que_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_CaPosiblesQueDataData"
respuesta_data: ["grupo_estudios:?string", "mi_grupo:string", "aCentrosNExt:array", "aCentrosAgdExt:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/ca_posibles_que.php"]
casos_uso: ["src\\actividadestudios\\application\\CaPosiblesQueData"]
tags: ["actividadestudios", "ca", "posibles", "que", "data"]
estado_revision: "revisado"
---

# Ca Posibles Que Data

Desplegables de centros (numerarios / agregados) y texto del grupo de estudios de la DL, para el
formulario de selección de `ca_posibles_que.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve el grupo de estudios de la delegación de la sesión (`OrbixRuntime::miDelef`) y la lista de
DL del grupo (`mi_grupo`). Construye dos desplegables de centros ordenados (sin acentos), cada uno
encabezado por `todos los ctr` + separador: uno con los centros de numerarios activos y otro con los
de agregados.

## Endpoint

- URL: `/src/actividadestudios/ca_posibles_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_que_data.php`

## Entrada

Sin parámetros POST: los datos se derivan de la delegación/sesión.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_CaPosiblesQueDataData`):
  - `grupo_estudios` (`?string`): grupo de estudios de la DL (o `null`).
  - `mi_grupo` (`string`): DL del grupo separadas por comas (o aviso si no hay grupo).
  - `aCentrosNExt` (`array`): desplegable de centros de numerarios (`id_ctr => nombre`; `1` = todos).
  - `aCentrosAgdExt` (`array`): desplegable de centros de agregados.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`ca_posibles_que.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\CaPosiblesQueData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/ca_posibles_que.php`