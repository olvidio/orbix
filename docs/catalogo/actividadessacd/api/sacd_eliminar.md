---
id: "actividadessacd.sacd_eliminar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_cargo:integer", "post.id_nom:integer"]
entrada_obligatoria: ["id_activ", "id_cargo"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se sabe cual borrar", "hay un error, no se ha eliminado el cargo", "hay un error, no se ha eliminado la asistencia"]
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdEliminar"]
tags: ["actividadessacd", "sacd", "eliminar"]
estado_revision: "revisado"
---

# Sacd Eliminar

Elimina el sacd (`{id_activ, id_cargo}`) de una actividad y, si se pasa `id_nom`, también la fila
de `Asistente` (`{id_activ, id_nom}`) asociada.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Valida `id_activ` e `id_cargo` (> 0).
- Busca el `ActividadCargo` por `{id_activ, id_cargo}` y lo elimina si existe.
- Si `id_nom > 0`, busca la `Asistencia` por `{id_activ, id_nom}` y la elimina si existe.
- Acumula los errores de ambas eliminaciones y los devuelve juntos.

## Endpoint

- URL: `/src/actividadessacd/sacd_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller (`inputInt`) | Si | Actividad |
| `id_cargo` | `integer` | controller (`inputInt`) | Si | Cargo sacd a borrar |
| `id_nom` | `integer` | controller (`inputInt`) | No | Si `> 0`, elimina también la asistencia asociada |

El controller construye `$input` con `id_activ`, `id_cargo` e `id_nom`.

## Salida

- Helper: `ContestarJson::enviar($useCase->execute($input), 'ok')` — el caso de uso devuelve el
  texto de error (vacío en éxito); `data` es el literal `"ok"`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina el `ActividadCargo` (`{id_activ, id_cargo}`) y, si procede, la `Asistencia` (`{id_activ, id_nom}`).

## Errores conocidos

- `no se sabe cual borrar` (falta `id_activ` o `id_cargo`)
- `hay un error, no se ha eliminado el cargo`
- `hay un error, no se ha eliminado la asistencia`

## Permisos

- Sin control propio en el caso de uso. Autorización en el frontend (`activ_sacd.php`): permiso de
  oficina `des` + `perm_modificar` por fila (`$_SESSION['oPermActividades']`), URL firmada con `HashFront`.

## Casos De Uso

- `src\actividadessacd\application\SacdEliminar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite `url_eliminar`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_orden` con opción `borrar`).
