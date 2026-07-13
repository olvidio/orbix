---
id: "ubis.centros_form_plazas"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_form_plazas"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_form_plazas.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosFormDataData"
respuesta_data: ["tipo_ctr:string", "tipo_labor:integer", "tipo_labor_bit_map:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_form_plazas.php"]
casos_uso: ["src\\ubis\\application\\CentrosFormData"]
tags: ["ubis", "centros", "form", "plazas"]
estado_revision: "revisado"
errores: []
---

# Centros Form Plazas

Carga datos del formulario modal de plazas y sede de un centro DL.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga datos del formulario modal de plazas y sede de un centro DL.

## Endpoint

- URL: `/src/ubis/centros_form_plazas`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_form_plazas.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `id_ubi`: id centro
  - `nombre_ubi`: nombre
  - `num_habit_indiv`: habitaciones
  - `plazas`: plazas
  - `sede`: boolean sede

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CentrosFormData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/centros_form_plazas.php"]`).
