---
id: "actividadessacd.sacd_reordenar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_reordenar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_reordenar.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.num_orden:string"]
entrada_obligatoria: ["id_activ", "id_nom", "num_orden"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom", "direccion de orden incorrecta (mas / menos)", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdReordenar"]
tags: ["actividadessacd", "sacd", "reordenar"]
estado_revision: "revisado"
---

# Sacd Reordenar

Sube o baja la prioridad de un sacd dentro del listado de cargos `sacd` de una actividad,
intercambiando el `id_nom` con el del cargo vecino (arriba/abajo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Valida `id_activ` e `id_nom` (> 0) y que `num_orden` sea `mas` o `menos`.
- Carga los `ActividadCargo` del grupo `sacd` de la actividad (orden por `id_cargo`).
- Localiza la fila del `id_nom` indicado e intercambia su `id_nom` con el del cargo anterior
  (`mas`) o posterior (`menos`), guardando ambas filas. Si el vecino tiene `id_nom = 0` no hace nada.

## Endpoint

- URL: `/src/actividadessacd/sacd_reordenar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_reordenar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller (`inputInt`) | Si | Actividad |
| `id_nom` | `integer` | controller (`inputInt`) | Si | Sacd a mover |
| `num_orden` | `string` | controller (`inputString`) | Si | Dirección: `mas` (sube prioridad) o `menos` (baja) |

El controller construye `$input` con `id_activ`, `id_nom` y `num_orden`. Nota: pese al nombre,
`num_orden` no es un número sino la dirección (`mas` / `menos`); el frontend `borrar` va por
`sacd_eliminar`, no por aquí.

## Salida

- Helper: `ContestarJson::enviar($useCase->execute($input), 'ok')` — el caso de uso devuelve el
  texto de error (vacío en éxito); `data` es el literal `"ok"`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `direccion de orden incorrecta (mas / menos)`
- `hay un error, no se ha guardado` (fallo al guardar alguna de las dos filas intercambiadas)

## Permisos

- Sin control propio en el caso de uso. Autorización en el frontend (`activ_sacd.php`): permiso de
  oficina `des` + `perm_modificar` por fila (`$_SESSION['oPermActividades']`), URL firmada con `HashFront`.

## Casos De Uso

- `src\actividadessacd\application\SacdReordenar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite `url_reordenar`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_orden` con `mas`/`menos`).
