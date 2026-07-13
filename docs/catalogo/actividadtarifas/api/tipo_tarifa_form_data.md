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
estado_revision: "revisado"
---

# Tipo Tarifa Form Data

Datos del formulario alta/edición de `TipoTarifa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Builder del popup de catálogo de tarifas. Alta: `id_tarifa` vacío o ausente → `es_nuevo=true`,
`id_tarifa='nuevo'`. Edición: carga `letra`, `modo`, `observ` del registro. Incluye
`opciones_modo` (`TarifaModoId::getArrayModo()`).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tarifa` | `string` | application | No | Vacío/`nuevo` = alta; numérico = edición |

## Salida

- Helper: `ContestarJson::enviar` → doble `JSON.parse` en cliente.
- Payload: `id_tarifa`, `es_nuevo`, `letra`, `modo`, `observ`, `opciones_modo`.

## Permisos

- Sin control propio; formulario accesible según permisos del listado (`puede_anadir` / enlace modificar).

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_form.php` → `tarifa_form.phtml`.
