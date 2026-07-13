---
id: "actividadessacd.sacd_asignar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer"]
entrada_obligatoria: ["id_activ", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom", "No puede haber tantos cargos de sacd en una actividad", "hay un error, no se ha guardado el cargo", "hay un error, no se ha guardado la asistencia"]
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdAsignar"]
tags: ["actividadessacd", "sacd", "asignar"]
estado_revision: "revisado"
---

# Sacd Asignar

Asigna un sacd (sacerdote) a una actividad creando un `ActividadCargo` con un `id_cargo` del
grupo `sacd`. Si la actividad es de sv, crea además la fila de `Asistente`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Valida que llegan `id_activ` e `id_nom` (> 0).
- Calcula el `id_cargo` a usar dentro del grupo de cargos `sacd` (`CargoRepository::getArrayCargos('sacd')`):
  primer hueco libre; si todos están ocupados, `max(id_cargo) + 1`. Si no cabe ninguno más devuelve error.
- Crea el `ActividadCargo` (`id_item` nuevo, `id_activ`, `id_nom`, `id_cargo`).
- Si la actividad es de sv (`id_tipo_activ` empieza por `1`), crea la `Asistencia` asociada
  (`propio = false`, `falta = false`, `dl_responsable = mi_delef()`).

Sucesor de la rama `asignar` del dispatcher legacy `apps/actividadessacd/controller/activ_sacd_ajax.php`
(referencia histórica de migración, no ruta actual).

## Endpoint

- URL: `/src/actividadessacd/sacd_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller (`inputInt`) | Si | Actividad destino |
| `id_nom` | `integer` | controller (`inputInt`) | Si | Persona (sacd) a asignar |

El controller construye `$input` explícitamente con `id_activ` e `id_nom` (`FuncTablasSupport::inputInt`).

## Salida

- Helper: `ContestarJson::enviar($useCase->execute($input), 'ok')` — el caso de uso devuelve el
  texto de error (vacío en éxito), que va como primer argumento (`error_txt`); `data` es el literal `"ok"`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.
- En error de negocio: `success: false`, `mensaje` con el texto traducido, `data: "ok"`.

## Efectos colaterales

- Crea un `ActividadCargo` del grupo `sacd`.
- En actividades de sv (`id_tipo_activ[0] === '1'`), crea también la fila de `Asistente`.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `No puede haber tantos cargos de sacd en una actividad`
- `hay un error, no se ha guardado el cargo`
- `hay un error, no se ha guardado la asistencia`

## Permisos

- El caso de uso no aplica control de permisos propio. La autorización se resuelve en el frontend
  (`activ_sacd.php`): la acción solo se ofrece con permiso de oficina `des`
  (`ActividadesPermSupport::havePermOficina('des')`) y con `perm_crear` por fila (calculado en
  `lista_actividades_sacd_data` vía `$_SESSION['oPermActividades']`). La URL se firma con `HashFront`.

## Casos De Uso

- `src\actividadessacd\application\SacdAsignar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite la URL firmada `url_asignar`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_asignar_sacd` hace el POST).
