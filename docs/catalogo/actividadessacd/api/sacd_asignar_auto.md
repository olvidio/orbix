---
id: "actividadessacd.sacd_asignar_auto"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_asignar_auto"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php"
entrada: ["post.f_ini_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_SacdAsignarAutoData"
respuesta_data: ["asignadas:integer", "sin_asignar:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/asignar_sacd_auto.php", "frontend/actividadessacd/view/asignar_sacd_auto.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdAsignarAuto"]
tags: ["actividadessacd", "sacd", "asignar", "auto"]
estado_revision: "revisado"
---

# Sacd Asignar Auto

Auto-asignación masiva: asigna el sacd titular del centro encargado a las actividades sr/sg
(`id_tipo_activ ~ .(4|5|7)`) `status = ACTUAL` con `f_ini > f_ini_iso` que aún no tienen sacd.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Si `f_ini_iso` está vacío, devuelve `{asignadas: 0, sin_asignar: 0}` sin hacer nada.
- Selecciona las actividades del periodo (tipos sr/sg, actuales, `f_ini > f_ini_iso`).
- Filtra las que no tienen ya un cargo del grupo `sacd`.
- Para cada una: si tiene un único centro encargado (`num_orden = 0`) y ese centro tiene un sacd
  titular (encargo `^1[12]00`, `EncargoSacd` con `modo 2|3` y `f_fin` nulo), crea el `ActividadCargo`
  (primer `id_cargo` del grupo `sacd`, `observ = 'auto'`).
- Devuelve el recuento de asignadas y de las que quedaron sin asignar.

Sucesor de `apps/actividadessacd/controller/asignar_sacd_auto.php` + `apps/actividadessacd/model/AsignarSacd.php`
(referencia histórica de migración, no ruta actual).

## Endpoint

- URL: `/src/actividadessacd/sacd_asignar_auto`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_ini_iso` | `string` | controller (`inputString`) | No | Fecha ISO (`YYYY-MM-DD`) de inicio de curso des; sin valor no procesa nada |

El controller construye `$input` con `f_ini_iso`.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($input))` — aquí `data` es el array resultado
  (serializado como string JSON; el front hace un segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_SacdAsignarAutoData`):
  - `asignadas` (`integer`): actividades a las que se ha asignado sacd.
  - `sin_asignar` (`integer`): actividades sin sacd que no se pudieron asignar (sin centro único o sin titular).

## Efectos colaterales

- Crea un `ActividadCargo` (`observ = 'auto'`) por cada actividad auto-asignada.

## Permisos

- Sin control propio en el caso de uso. La pantalla `asignar_sacd_auto.php` firma la URL con `HashFront`;
  la autorización de acceso se resuelve en el menú/frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadessacd\application\SacdAsignarAuto`

## Frontend Relacionado

- `frontend/actividadessacd/controller/asignar_sacd_auto.php` (emite `url_asignar_auto` y calcula `f_ini_iso`).
- `frontend/actividadessacd/view/asignar_sacd_auto.phtml` (`fnjs_asignar_sacd_auto` hace el POST y pinta el resultado).
