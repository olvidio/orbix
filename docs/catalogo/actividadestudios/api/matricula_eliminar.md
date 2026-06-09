---
id: "actividadestudios.matricula_eliminar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_nom:integer", "post.id_pau:integer", "post.pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no encuentro la matricula", "hay un error, no se ha borrado"]
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_pendientes.php", "frontend/actividadestudios/view/matriculas.phtml"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaEliminar"]
tags: ["actividadestudios", "matricula", "eliminar"]
estado_revision: "generado"
---

# Matricula Eliminar

Elimina una o varias matriculas y reajusta los dossiers 1303 / 3103 y las asignaturas impartidas (`ActividadAsignatura`). Sustituye al case `eliminar` del antiguo `update_3103.php` dispatcher.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/matricula_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `pau` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina una o varias matriculas y reajusta los dossiers 1303 / 3103 y las asignaturas impartidas (`ActividadAsignatura`).
- Sustituye al case `eliminar` del antiguo `update_3103.php` dispatcher.

## Errores conocidos

- `no encuentro la matricula`
- `hay un error, no se ha borrado`

## Casos De Uso

- `src\actividadestudios\application\MatriculaEliminar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_pendientes.php`
- `frontend/actividadestudios/view/matriculas.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.