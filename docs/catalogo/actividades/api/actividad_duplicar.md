---
id: "actividades.actividad_duplicar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_duplicar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_duplicar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["post.sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se ha seleccionado ninguna actividad", "actividad no encontrada", "no se puede duplicar actividades que no sean de la propia dl", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividades/view/actividades.js"]
casos_uso: ["src\\actividades\\application\\ActividadDuplicar"]
tags: ["actividades", "actividad", "duplicar"]
estado_revision: "revisado"
---

# Actividad Duplicar

Duplica **solo la primera** actividad seleccionada (`sel[0]`, aunque haya varias
marcadas). La copia se crea en la tabla de la propia dl con:

- nombre prefijado: `dup <nombre original>`
- `status = PROYECTO`
- id nuevo (`getNewIdActividad`)

Restriccion de origen: la actividad debe ser de la propia delegacion
(`dl_org = mi_delef`), **o bien** de `mi_dele + 'f'` si el usuario tiene permiso
de oficina `des` (caso sf desde sv para usuarios des).

## Endpoint

- URL: `/src/actividades/actividad_duplicar`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_duplicar.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `sel` | `array` | Si | Solo se usa el primer elemento (`id` o `id#extra`). |

## Salida

- Helper: `ContestarJson::enviar`
- Exito: `success: true` (sin payload).
- Error: `success: false`, `mensaje` con el texto.

## Permisos

- No exige permiso para duplicar actividades de la propia dl (control en la UI:
  la accion esta en los listados de seleccion).
- El permiso oficina `des` (`$_SESSION['oPerm']`) solo amplia el origen permitido
  a `mi_dele + 'f'`.

## Errores conocidos

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`
- `hay un error, no se ha guardado` + detalle

## Casos De Uso

- `src\actividades\application\ActividadDuplicar`

## Frontend Relacionado

- `frontend/actividades/view/actividades.js` — `jsForm.update(form, 'duplicar')`
  (con confirmacion JS), usado desde los listados (`actividad_select`).

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadDuplicar`): solo duplica la primera
  seleccion; restriccion de dl y matiz del permiso `des` verificados.
- Pendiente: ejemplos reales de request/response.
