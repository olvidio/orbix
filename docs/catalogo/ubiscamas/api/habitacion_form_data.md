---
id: "ubiscamas.habitacion_form_data"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/habitacion_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/habitacion_form_data.php"
entrada: ["post.id_habitacion:string", "post.id_ubi:integer", "post.nuevo:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/controller/habitacion_form.php", "frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php"]
casos_uso: ["src\ubiscamas\application\HabitacionFormData"]
tags: ["ubiscamas", "habitacion", "form", "data"]
estado_revision: "revisado"
errores: []
---

# Habitacion Form Data

Prepara el formulario de alta o edición de una habitación en un ubi CDC. Con `nuevo` vacío y `sel` o `id_habitacion` carga la habitación y sus camas; sin id genera orden siguiente y defaults (1 cama, 1 VIP). `sel` usa token `id_habitacion#...`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Prepara el formulario de alta o edición de una habitación en un ubi CDC. Con `nuevo` vacío y `sel` o `id_habitacion` carga la habitación y sus camas; sin id genera orden siguiente y defaults (1 cama, 1 VIP). `sel` usa token `id_habitacion#...`.

## Endpoint

- URL: `/src/ubiscamas/habitacion_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_habitacion` | `string` | application | No |  |
| `id_ubi` | `integer` | application | No |  |
| `nuevo` | `string` | application | No |  |
| `sel` | `array` | application | No | Token `id_habitacion#...` o `id_cama#...` según endpoint |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `hash_form`: config HashFront (campos_form/chk/no/hidden)
  - `hash_actualizar`: config HashFront para submit habitacion_update
  - `cama_form_hash`: url y campos hacia cama_form
  - `cama_delete_hash`: url y campos hacia cama_delete
  - `id_habitacion`: uuid o vacío en alta
  - `id_ubi`: ubi CDC
  - `orden`: entero orden
  - `nombre`: nombre habitación
  - `numero_camas`: total camas previstas
  - `numero_camas_vip`: camas VIP previstas
  - `planta`: planta
  - `sillon`: boolean
  - `adaptada`: boolean
  - `observaciones`: texto
  - `despacho`: boolean
  - `tipoLavabo`: entero código
  - `a_tipos_tipoLavabo`: mapa código=>etiqueta
  - `a_camas`: filas {id_cama, descripcion, larga, vip}

## Errores conocidos

- _(ninguno documentado)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- `src\ubiscamas\application\HabitacionFormData`

## Frontend Relacionado

- `frontend/ubiscamas/controller/habitacion_form.php`
- `frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php`
