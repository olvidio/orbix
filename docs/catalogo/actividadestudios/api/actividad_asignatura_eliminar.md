---
id: "actividadestudios.actividad_asignatura_eliminar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/actividad_asignatura_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["sólo se puede eliminar una asignatura desde el dossier de la actividad", "faltan claves de la asignatura de actividad", "no encuentro la asignatura", "hay un error, no se ha borrado"]
frontend_referencias: []
casos_uso: ["src\\actividadestudios\\application\\ActividadAsignaturaEliminar"]
tags: ["actividadestudios", "actividad", "asignatura", "eliminar"]
estado_revision: "generado"
---

# Actividad Asignatura Eliminar

Elimina una `ActividadAsignatura` (asignatura impartida en un ca). Sustituye al case `eliminar` del antiguo `update_3005.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/actividad_asignatura_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `pau` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina una `ActividadAsignatura` (asignatura impartida en un ca).
- Sustituye al case `eliminar` del antiguo `update_3005.php` dispatcher.

## Errores conocidos

- `sólo se puede eliminar una asignatura desde el dossier de la actividad`
- `faltan claves de la asignatura de actividad`
- `no encuentro la asignatura`
- `hay un error, no se ha borrado`

## Casos De Uso

- `src\actividadestudios\application\ActividadAsignaturaEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.