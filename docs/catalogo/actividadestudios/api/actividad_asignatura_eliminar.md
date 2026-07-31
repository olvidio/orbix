---
id: "actividadestudios.actividad_asignatura_eliminar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/actividad_asignatura_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.pau:string", "post.sel:array"]
entrada_obligatoria: ["pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["sólo se puede eliminar una asignatura desde el dossier de la actividad", "faltan claves de la asignatura de actividad", "no encuentro la asignatura", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaEliminar"]
tags: ["actividadestudios", "actividad", "asignatura", "eliminar"]
estado_revision: "revisado"
---

# Actividad Asignatura Eliminar

Elimina una `ActividadAsignatura` (asignatura impartida en un ca). Sustituye al case `eliminar` del
antiguo `update_3005.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la asignatura de actividad identificada por `(id_activ, id_asignatura)`. Solo permite la
operación cuando `pau === 'a'` (dossier de actividad).

**Casos particulares**

- **Selección desde listado:** si llegan `sel` (array) y `pau === 'a'`, toma el primer token
  `id_activ#id_asignatura` y sobrescribe las claves.
- **`pau` distinto de `'a'`:** rechaza con error (no se elimina desde dossier de persona u otro).

## Endpoint

- URL: `/src/actividadestudios/actividad_asignatura_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pau` | `string` | application | Sí | Debe ser `'a'` (dossier actividad) |
| `id_activ` | `integer` | application | Condicional | Obligatorio si no viene en `sel` |
| `id_asignatura` | `integer` | application | Condicional | Obligatorio si no viene en `sel` |
| `sel` | `array` | application | No | Primer elemento `id_activ#id_asignatura` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina el registro `ActividadAsignatura` correspondiente.

## Errores conocidos

- `sólo se puede eliminar una asignatura desde el dossier de la actividad`
- `faltan claves de la asignatura de actividad`
- `no encuentro la asignatura`
- `hay un error, no se ha borrado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\ActividadAsignaturaEliminar`

## Frontend Relacionado

- Invocado desde el form/listado de asignaturas de actividad (URL típica en payload
  `url_actividad_asignatura_eliminar`). Referencia de pantalla:
  `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`.
