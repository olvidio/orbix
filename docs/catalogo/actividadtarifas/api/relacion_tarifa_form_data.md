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
estado_revision: "generado"
---

# Relacion Tarifa Form Data

Endpoint backend: datos del formulario modificar/nuevo de `RelacionTarifaTipoActividad`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `actividadtarifas_RelacionTarifaFormDataData`):
  - `es_nuevo` (`boolean`)
  - `id_item` (`string`)
  - `id_tipo_activ` (`integer`)
  - `nom_tipo_activ` (`string`)
  - `isfsv` (`integer`)
  - `id_tarifa_sel` (`integer`)
  - `opciones_tarifa` (`array`)

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.