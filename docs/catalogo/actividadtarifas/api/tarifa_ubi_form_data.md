---
id: "actividadtarifas.tarifa_ubi_form_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php"
entrada: ["post.id_item:string", "post.id_ubi:integer", "post.letra:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_TarifaUbiFormDataData"
respuesta_data: ["es_nuevo:boolean", "id_item:string", "id_ubi:integer", "year:integer", "letra:string", "cantidad:string", "opciones_tarifa:array", "opciones_serie:array", "id_serie_sel:integer", "token_update:string", "token_eliminar:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiFormData"]
tags: ["actividadtarifas", "tarifa", "ubi", "form", "data"]
estado_revision: "revisado"
---

# Tarifa Ubi Form Data

Datos del formulario alta/edición de `TarifaUbi` y emisión de cápsulas `HashB`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Builder del popup de tarifa por casa/año. Alta: `id_item` vacío, desplegables de tarifa y serie,
contexto `{id_ubi, year}`. Edición: carga `cantidad` del registro, título con `letra`, contexto
`{id_item, id_ubi, year}` y token de eliminar. Firma `token_update` / `token_eliminar` para las
mutaciones posteriores.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | application | No | Vacío = alta; numérico = edición |
| `id_ubi` | `integer` | application | No | Casa (obligatoria en alta) |
| `year` | `integer` | application | No | Año calendario |
| `letra` | `string` | application | No | Título; por defecto `nueva` si vacío |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data` con doble `JSON.parse` en cliente.
- Payload en `data`:
  - `es_nuevo`, `id_item`, `id_ubi`, `year`, `letra`, `cantidad`
  - `opciones_tarifa` (solo alta; filtradas por `mi_sfsv`)
  - `opciones_serie`, `id_serie_sel` (por defecto `SerieId::GENERAL`)
  - `token_update`, `token_eliminar` (cápsulas `HashB` opacas para `ctx_update` / `ctx_eliminar`)

## Permisos

- Sin control propio; el listado solo muestra enlace modificar con `have_perm_oficina('adl')` y
  sección coincidente.

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi_form.php` → `tarifa_ubi_form.phtml` inyecta
  `token_update` y `token_eliminar` como hidden `ctx_*`.
