---
id: "ubiscamas.cama_form_data"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/cama_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/cama_form_data.php"
entrada: ["post.id_cama:string", "post.id_habitacion:string", "post.id_ubi:integer", "post.mod:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/controller/cama_form.php", "frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php"]
casos_uso: ["src\ubiscamas\application\CamaFormData"]
tags: ["ubiscamas", "cama", "form", "data"]
estado_revision: "revisado"
errores: []
---

# Cama Form Data

Carga el modal de edición de una cama. Si `id_cama` vacío genera UUID nuevo; si existe lee descripción, larga y VIP del repositorio.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga el modal de edición de una cama. Si `id_cama` vacío genera UUID nuevo; si existe lee descripción, larga y VIP del repositorio.

## Endpoint

- URL: `/src/ubiscamas/cama_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cama` | `string` | application | No |  |
| `id_habitacion` | `string` | application | No |  |
| `id_ubi` | `integer` | application | No |  |
| `mod` | `string` | application | No |  |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `hash_form`: config HashFront descripcion/larga/vip
  - `id_cama`: uuid (nuevo si vacío)
  - `id_habitacion`: uuid habitación
  - `id_ubi`: ubi
  - `descripcion`: texto cama
  - `larga`: boolean
  - `vip`: boolean

## Errores conocidos

- _(ninguno documentado)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía frontend + `$_SESSION['oPerm']` y permisos del dossier/actividad padre.

## Casos De Uso

- `src\ubiscamas\application\CamaFormData`

## Frontend Relacionado

- `frontend/ubiscamas/controller/cama_form.php`
- `frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php`
