---
id: "actividadcargos.cargo_eliminar"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/cargo_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadcargos/infrastructure/ui/http/controllers/cargo_eliminar.php"
entrada: ["post.elim_asis:integer", "post.id_item:integer", "post.sel:array"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_item", "no encuentro el cargo", "hay un error, no se ha eliminado", "hay un error, no se ha eliminado el asistente"]
frontend_referencias: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoEliminar"]
tags: ["actividadcargos", "cargo", "eliminar"]
estado_revision: "generado"
---

# Cargo Eliminar

Elimina un `ActividadCargo` y, si procede, su `Asistente`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadcargos/cargo_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `elim_asis` | `integer` | application | No | application |
| `id_item` | `integer` | application | Si | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un `ActividadCargo` y, si `elim_asis === 2` y el tipo de actividad es `s`/`sg`, elimina tambien el `Asistente` y cierra los dossiers 1301/3101.
- Sustituye al case `eliminar` del antiguo `update_3102.php` dispatcher.

## Errores conocidos

- `falta id_item`
- `no encuentro el cargo`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha eliminado el asistente`

## Casos De Uso

- `src\actividadcargos\application\ActividadCargoEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.