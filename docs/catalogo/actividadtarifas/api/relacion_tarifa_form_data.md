---
id: "actividadtarifas.relacion_tarifa_form_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_form_data.php"
entrada: ["post.id_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_RelacionTarifaFormDataData"
respuesta_data: ["es_nuevo:boolean", "id_item:string", "id_tipo_activ:integer", "nom_tipo_activ:string", "isfsv:integer", "id_tarifa_sel:integer", "opciones_tarifa:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaFormData"]
tags: ["actividadtarifas", "relacion", "tarifa", "form", "data"]
estado_revision: "revisado"
---

# Relacion Tarifa Form Data

Datos del formulario alta/edición de `RelacionTarifaTipoActividad`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Builder del popup tarifa↔tipo actividad. Alta: `id_item` vacío/`nuevo`, `isfsv = mi_sfsv`,
opciones de tarifa filtradas por sección. Edición: carga tipo de actividad, nombre y tarifa
seleccionada. En alta el selector de tipo de actividad viene de `actividad_que_datos` (otro módulo).

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | application | No | Vacío/`nuevo` = alta |

## Salida

- Helper: `ContestarJson::enviar` → doble `JSON.parse` en cliente.
- Payload: `es_nuevo`, `id_item`, `id_tipo_activ`, `nom_tipo_activ`, `isfsv`, `id_tarifa_sel`,
  `opciones_tarifa`.

## Permisos

- Sin control propio; visibilidad según listado (`puede_anadir`, enlace modificar con `adl`).

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`: en alta combina con
  `/src/actividades/actividad_que_datos` (`para=tipoactiv-tarifas`).
