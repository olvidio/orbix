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
estado_revision: "generado"
---

# Tarifa Ubi Form Data

Endpoint backend: datos del formulario modificar/nuevo de `TarifaUbi`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `letra` | `string` | controller+application | No | controller+application |
| `year` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `actividadtarifas_TarifaUbiFormDataData`):
  - `es_nuevo` (`boolean`)
  - `id_item` (`string`)
  - `id_ubi` (`integer`)
  - `year` (`integer`)
  - `letra` (`string`)
  - `cantidad` (`string`)
  - `opciones_tarifa` (`array`)
  - `opciones_serie` (`array`)
  - `id_serie_sel` (`integer`)
  - `token_update` (`string`)
  - `token_eliminar` (`string`)

## Efectos colaterales

- Junto con los datos del form, emite las **cápsulas `HashB`** que el navegador transportará opacamente y que los endpoints de mutación (`tarifa_ubi_update`, `tarifa_ubi_eliminar`) abrirán para recuperar el contexto firmado (`id_item`, `id_ubi`, `year`).

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.