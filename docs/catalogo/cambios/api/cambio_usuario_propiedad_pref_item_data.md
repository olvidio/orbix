---
id: "cambios.cambio_usuario_propiedad_pref_item_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_propiedad_pref_item_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_item_data.php"
entrada: ["post.id_item:integer", "post.objeto:string", "post.propiedad:string"]
entrada_obligatoria: ["propiedad"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref_condicion.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefItemData"]
tags: ["cambios", "cambio", "usuario", "propiedad", "pref", "item", "data"]
estado_revision: "revisado"
---

# Cambio Usuario Propiedad Pref Item Data

Datos del modal de condición para una propiedad (`id_item` existente o nueva).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga operador, valor y checkboxes old/new de una `CambioUsuarioPropiedadPref` si `id_item > 0`. Si la
propiedad es `id_ubi`, añade `aOpcionesCasas` filtradas por rol/permisos de oficina.

## Endpoint

- URL: `/src/cambios/cambio_usuario_propiedad_pref_item_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_item_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `propiedad` | `string` | controller+application | Sí | Nombre del campo vigilado |
| `objeto` | `string` | controller+application | No | Objeto padre |
| `id_item` | `integer` | controller+application | No | `0` = condición nueva |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `error`, `id_item`, `objeto`, `propiedad`
  - `valor`, `operador`, `chk_old`, `chk_new`
  - `aOpcionesCasas` (`array`, solo si `propiedad=id_ubi`)

## Permisos

- Filtro de casas: rol `PAU_CDC` (solo sus ubicaciones), permisos oficina `des`/`vcsd` (todas
  activas), o restricción `sv`/`sf` según `mi_sfsv()`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioPropiedadPrefItemData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref_condicion.php`: modal AJAX; `fnjs_modificar` en la
  tabla de propiedades.
