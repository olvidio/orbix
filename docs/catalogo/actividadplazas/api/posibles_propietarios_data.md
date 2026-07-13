---
id: "actividadplazas.posibles_propietarios_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/posibles_propietarios_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/posibles_propietarios_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer"]
entrada_obligatoria: ["id_nom", "id_activ"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PosiblesPropietariosDataData"
respuesta_data: ["id:string", "opciones:array", "selected:string", "blanco:boolean", "val_blanco:string"]
requiere_hashb: false
errores: ["faltan parametros id_nom / id_activ", "No se encuentra persona con id_nom <id>"]
frontend_referencias: ["frontend/asistentes/helpers/FormActividadesDeUnaPersonaRender.php", "frontend/asistentes/helpers/FormAsistentesAUnaActividadRender.php"]
casos_uso: ["src\\actividadplazas\\application\\PosiblesPropietariosData"]
tags: ["actividadplazas", "posibles", "propietarios", "data"]
estado_revision: "revisado"
---

# Posibles Propietarios Data

Data builder del desplegable "posibles propietarios de plaza" para una persona + actividad. Devuelve
el payload estándar de desplegable (`id`, `opciones`, `selected`, `blanco`, `val_blanco`) que el
frontend monta con `fnjs_construir_desplegable`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Localiza la persona (`Persona::findPersonaEnGlobal`) y su colectivo (`obj_pau`).
- Para `PersonaEx` (de paso) usa su `dl` como "dl de paso".
- Fija el propietario preseleccionado como `mi_delef()>[dl_de_paso]`.
- Delega en `ResumenPlazasService::getPosiblesPropietariosOpciones()` las opciones de dl propietarias
  posibles y las ordena con `OpcionesDesplegable::enOrden`.
- Errores de negocio: faltan `id_nom`/`id_activ`, o no se encuentra la persona. En ese caso el
  controller responde `success: false` con `data: "ko"`.

## Endpoint

- URL: `/src/actividadplazas/posibles_propietarios_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/posibles_propietarios_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Si | Persona a la que se asigna plaza; `0` → error de negocio |
| `id_activ` | `integer` | controller | Si | Actividad de la plaza; `0` → error de negocio |

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadplazas_PosiblesPropietariosDataData`):
  - `id` (`string`): `"propietario"` (id del `<select>`).
  - `opciones` (`array`): lista ordenada `[valor, etiqueta]` de dl propietarias posibles.
  - `selected` (`string`): propietario preseleccionado (`mi_delef()>[dl_de_paso]`).
  - `blanco` (`boolean`): `true` (incluir opción en blanco).
  - `val_blanco` (`string`): valor de la opción en blanco (`""`).
- En error: `success: false`, `mensaje` con el texto, `data: "ko"`.

## Errores conocidos

- `faltan parametros id_nom / id_activ`
- `No se encuentra persona con id_nom <id>`

## Permisos

- Sin control de permisos propio; se invoca desde los formularios de asistentes al asignar plaza y la
  autorización de oficina se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PosiblesPropietariosData`

## Frontend Relacionado

- Invocado desde `apps/asistentes` al asignar plaza a una asistencia. La URL se emite en el payload de
  los formularios (`posibles_propietarios_data`) y se consume en
  `frontend/asistentes/helpers/FormActividadesDeUnaPersonaRender.php` y
  `frontend/asistentes/helpers/FormAsistentesAUnaActividadRender.php`.
