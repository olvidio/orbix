---
id: "actividadplazas.resumen_plazas_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/resumen_plazas_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/resumen_plazas_data.php"
entrada: ["post.id_activ:integer", "post.nom_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_ResumenPlazasDataData"
respuesta_data: ["id_activ:integer", "nom_activ:string", "publicado:boolean", "otra_dl:boolean", "a_plazas:array", "plazas_totales:integer", "tot_calendario:integer", "tot_cedidas:integer", "tot_conseguidas:integer", "tot_disponibles:integer", "tot_ocupadas:integer", "dl_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/resumen_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\ResumenPlazasData"]
tags: ["actividadplazas", "resumen", "plazas", "data"]
estado_revision: "generado"
---

# Resumen Plazas Data

Endpoint backend: datos del resumen de plazas por actividad (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) + opciones del desplegable para "ceder" y flags publicado/otra_dl.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/resumen_plazas_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/resumen_plazas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `nom_activ` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_ResumenPlazasDataData`):
  - `id_activ` (`integer`)
  - `nom_activ` (`string`)
  - `publicado` (`boolean`)
  - `otra_dl` (`boolean`)
  - `a_plazas` (`array`)
  - `plazas_totales` (`integer`)
  - `tot_calendario` (`integer`)
  - `tot_cedidas` (`integer`)
  - `tot_conseguidas` (`integer`)
  - `tot_disponibles` (`integer`)
  - `tot_ocupadas` (`integer`)
  - `dl_opciones` (`array`)

## Casos De Uso

- `src\actividadplazas\application\ResumenPlazasData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/resumen_plazas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.