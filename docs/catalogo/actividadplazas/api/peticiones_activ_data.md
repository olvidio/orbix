---
id: "actividadplazas.peticiones_activ_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_activ_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_activ_data.php"
entrada: ["post.id_ctr_agd:integer", "post.id_ctr_n:integer", "post.id_nom:integer", "post.na:string", "post.que:string", "post.sactividad:string", "post.todos:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PeticionesActivDataData"
respuesta_data: ["id_nom:integer", "ap_nom:string", "na:string", "sactividad:string", "sid_activ:string", "opciones:array", "tipo:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesActivData"]
tags: ["actividadplazas", "peticiones", "activ", "data"]
estado_revision: "generado"
---

# Peticiones Activ Data

Endpoint backend: lista de actividades candidatas + peticiones actuales para una persona+tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/peticiones_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ctr_agd` | `integer` | controller+application | No | controller+application |
| `id_ctr_n` | `integer` | controller+application | No | controller+application |
| `id_nom` | `integer` | controller+application | No | controller+application |
| `na` | `string` | controller+application | No | controller+application |
| `que` | `string` | controller+application | No | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |
| `todos` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_PeticionesActivDataData`):
  - `id_nom` (`integer`)
  - `ap_nom` (`string`)
  - `na` (`string`)
  - `sactividad` (`string`)
  - `sid_activ` (`string`)
  - `opciones` (`array`)
  - `tipo` (`string`)

## Casos De Uso

- `src\actividadplazas\application\PeticionesActivData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.