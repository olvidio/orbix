---
id: "ubis.ubis_editar_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_editar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_editar_data.php"
entrada: ["post.dl:string", "post.obj_pau:string", "post.region:string", "post.tipo_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisEditarOpcionesDataData"
respuesta_data: ["opciones_dl:array", "opciones_region:array", "opciones_tipo_ctr:array", "opciones_tipo_casa:array", "opciones_id_ctr_padre:array"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_editar.php"]
casos_uso: ["src\\ubis\\application\\UbisEditarOpcionesData"]
tags: ["ubis", "editar", "data"]
estado_revision: "generado"
---

# Ubis Editar Data

Opciones de desplegables para frontend/ubis/controller/ubis_editar.php

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_editar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `tipo_ubi` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_UbisEditarOpcionesDataData`):
  - `opciones_dl` (`array`)
  - `opciones_region` (`array`)
  - `opciones_tipo_ctr` (`array`)
  - `opciones_tipo_casa` (`array`)
  - `opciones_id_ctr_padre` (`array`)

## Casos De Uso

- `src\ubis\application\UbisEditarOpcionesData`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_editar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.