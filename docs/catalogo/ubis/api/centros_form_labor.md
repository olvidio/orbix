---
id: "ubis.centros_form_labor"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_form_labor"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_form_labor.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosFormDataData"
respuesta_data: ["tipo_ctr:string", "tipo_labor:integer", "tipo_labor_bit_map:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_form_labor.php"]
casos_uso: ["src\\ubis\\application\\CentrosFormData"]
tags: ["ubis", "centros", "form", "labor"]
estado_revision: "generado"
---

# Centros Form Labor

Datos comunes para los formularios de centro dl (labor / num / plazas). Los tres formularios muestran sobre un mismo centro un subconjunto de campos distinto según el modo indicado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/centros_form_labor`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_form_labor.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

Nota: el controller tambien lee `$_GET` directamente.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_CentrosFormDataData`):
  - `tipo_ctr` (`string`)
  - `tipo_labor` (`integer`)
  - `tipo_labor_bit_map` (`array`)

## Casos De Uso

- `src\ubis\application\CentrosFormData`

## Frontend Relacionado

- `frontend/ubis/controller/centros_form_labor.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.