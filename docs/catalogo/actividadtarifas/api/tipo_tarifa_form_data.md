---
id: "actividadtarifas.tipo_tarifa_form_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_form_data.php"
entrada: ["post.id_tarifa:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_TipoTarifaFormDataData"
respuesta_data: ["id_tarifa:string", "es_nuevo:boolean", "letra:string", "modo:integer", "observ:string", "opciones_modo:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaFormData"]
tags: ["actividadtarifas", "tipo", "tarifa", "form", "data"]
estado_revision: "generado"
---

# Tipo Tarifa Form Data

Endpoint backend: datos del form modificar/nuevo `TipoTarifa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tarifa` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `actividadtarifas_TipoTarifaFormDataData`):
  - `id_tarifa` (`string`)
  - `es_nuevo` (`boolean`)
  - `letra` (`string`)
  - `modo` (`integer`)
  - `observ` (`string`)
  - `opciones_modo` (`array`)

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.