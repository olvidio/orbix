---
id: "actividadestudios.profesores_desplegable_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/profesores_desplegable_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/profesores_desplegable_data.php"
entrada: ["post.salida:string", "post.id_asignatura:integer", "post.id_activ:integer", "post.id_profesor:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_ProfesoresDesplegableDataData"
respuesta_data: ["id:string", "opciones:array", "blanco:boolean", "val_blanco:string", "selected:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ProfesoresDesplegableData"]
tags: ["actividadestudios", "profesores", "desplegable", "data"]
estado_revision: "revisado"
---

# Profesores Desplegable Data

Devuelve los datos (`id`, `opciones`, `selected`, `blanco`) para (re)construir el `<select>` de
profesores en el form de `ActividadAsignatura` (llamada AJAX al cambiar el ámbito).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según `salida`, arma la lista de profesores candidatos:

- `asignatura`: profesores que imparten esa asignatura (`id_asignatura`).
- `dl`: profesores de la actividad (`id_activ`).
- `todos`: todos los profesores publicados de la STGR.

Si `id_profesor` no está en la lista, lo antepone (con separador) para no perder al asignado. El
`selected` es `id_profesor` o `-1` si no hay.

## Endpoint

- URL: `/src/actividadestudios/profesores_desplegable_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/profesores_desplegable_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `salida` | `string` | application | No | Ámbito: `asignatura` / `dl` / `todos` (otro valor → lista vacía) |
| `id_asignatura` | `integer` | application | No | Usado con `salida=asignatura` |
| `id_activ` | `integer` | application | No | Usado con `salida=dl` |
| `id_profesor` | `integer` | application | No | Profesor preseleccionado; se garantiza que aparezca |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_ProfesoresDesplegableDataData`):
  - `id` (`string`): siempre `id_profesor`.
  - `opciones` (`array`): pares `[valor, etiqueta]` ordenados.
  - `blanco` (`boolean`): siempre `true` (opción en blanco).
  - `val_blanco` (`string`): valor de la opción en blanco (`''`).
  - `selected` (`integer`): `id_profesor` o `-1`.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_asignaturas_de_una_actividad.php`) y en `$_SESSION['oPerm']`. No inferir permisos aquí.

## Casos De Uso

- `src\actividadestudios\application\ProfesoresDesplegableData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`