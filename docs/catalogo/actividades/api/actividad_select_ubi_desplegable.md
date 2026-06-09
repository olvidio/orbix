---
id: "actividades.actividad_select_ubi_desplegable"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_select_ubi_desplegable"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php"
entrada: ["post.dl_org:string", "post.isfsv:integer", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadSelectUbiDataData"
respuesta_data: ["opcionesFreq:array", "opcionesRegion:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/view/actividad_select_ubi.phtml"]
casos_uso: ["src\\actividades\\application\\ActividadSelectUbiData"]
tags: ["actividades", "actividad", "select", "ubi", "desplegable"]
estado_revision: "generado"
---

# Actividad Select Ubi Desplegable

Endpoint backend que devuelve las opciones (value => label) de los desplegables de la pantalla "seleccionar lugar para una actividad".

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_select_ubi_desplegable`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_org` | `string` | controller+application | No | controller+application |
| `isfsv` | `integer` | controller+application | No | controller+application |
| `tipo` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadSelectUbiDataData`):
  - `opcionesFreq` (`array`)
  - `opcionesRegion` (`array`)

## Casos De Uso

- `src\actividades\application\ActividadSelectUbiData`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/view/actividad_select_ubi.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.