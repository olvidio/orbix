---
id: "ubiscamas.habitacion_update"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/habitacion_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/habitacion_update.php"
entrada: ["post.sel:array", "post.id_habitacion:string", "post.id_ubi:integer", "post.orden:integer", "post.nombre:string", "post.numero_camas:integer", "post.numero_camas_vip:integer", "post.planta:string", "post.tipoLavabo:integer", "post.sillon:string", "post.adaptada:string", "post.observaciones:string", "post.despacho:string", "post.new_camas_desc:array", "post.new_camas_larga:array", "post.new_camas_vip:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/view/habitacion_form.phtml", "frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php"]
casos_uso: []
tags: ["ubiscamas", "habitacion", "update"]
estado_revision: "revisado"
errores: ["Habitación no válida", "Error al guardar la habitación"]
---

# Habitacion Update

Alta o actualización de habitación CDC y sincronización de camas: guarda datos de habitación, crea camas desde `new_camas_*` y auto-genera camas hasta alcanzar `numero_camas` (marcando VIP hasta `numero_camas_vip`). `sel` puede traer `id_habitacion` como primer token.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta o actualización de habitación CDC y sincronización de camas: guarda datos de habitación, crea camas desde `new_camas_*` y auto-genera camas hasta alcanzar `numero_camas` (marcando VIP hasta `numero_camas_vip`). `sel` puede traer `id_habitacion` como primer token.

## Endpoint

- URL: `/src/ubiscamas/habitacion_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_habitacion#...` o `id_cama#...` según endpoint |
| `id_habitacion` | `string` | application | No |  |
| `id_ubi` | `integer` | application | No |  |
| `orden` | `integer` | application | No |  |
| `nombre` | `string` | application | No |  |
| `numero_camas` | `integer` | application | No |  |
| `numero_camas_vip` | `integer` | application | No |  |
| `planta` | `string` | application | No |  |
| `tipoLavabo` | `integer` | application | No |  |
| `sillon` | `string` | application | No |  |
| `adaptada` | `string` | application | No |  |
| `observaciones` | `string` | application | No |  |
| `despacho` | `string` | application | No |  |
| `new_camas_desc` | `array` | application | No |  |
| `new_camas_larga` | `array` | application | No |  |
| `new_camas_vip` | `array` | application | No |  |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado en mutaciones).

## Errores conocidos
- `Habitación no válida`
- `Error al guardar la habitación`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- Lógica inline en el controller (sin caso de uso en `application/`).

## Frontend Relacionado

- `frontend/ubiscamas/view/habitacion_form.phtml`
- `frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php`
