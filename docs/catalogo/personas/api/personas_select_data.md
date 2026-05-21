---
id: "personas.personas_select_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/personas_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/personas_select_data.php"
entrada: ["post.apellido1:string", "post.apellido2:string", "post.centro:string", "post.cmb:string", "post.es_sacd:integer", "post.exacto:string", "post.na:string", "post.nombre:string", "post.tabla:string", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "personas_PersonasSelectDataData"
respuesta_data: ["id_nom:integer", "id_tabla:string", "nom:string", "nombre_ubi:string", "nivel_stgr:string", "situacion:string", "f_situacion:string"]
requiere_hashb: false
frontend_referencias: ["frontend/personas/controller/personas_select.php"]
casos_uso: ["src\\personas\\application\\PersonasSelectData"]
tags: ["personas", "select", "data"]
estado_revision: "generado"
---

# Personas Select Data

Endpoint JSON: datos crudos para la tabla `personas_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/personas_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/personas_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `apellido1` | `string` | application | No | application |
| `apellido2` | `string` | application | No | application |
| `centro` | `string` | application | No | application |
| `cmb` | `string` | application | No | application |
| `es_sacd` | `integer` | application | No | application |
| `exacto` | `string` | application | No | application |
| `na` | `string` | application | No | application |
| `nombre` | `string` | application | No | application |
| `tabla` | `string` | controller+application | No | controller+application |
| `tipo` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `personas_PersonasSelectDataData`):
  - `id_nom` (`integer`)
  - `id_tabla` (`string`)
  - `nom` (`string`)
  - `nombre_ubi` (`string`)
  - `nivel_stgr` (`string`)
  - `situacion` (`string`)
  - `f_situacion` (`string`)

## Permisos

- Permiso oficina `dtor`
- Permiso oficina `des`
- Permiso oficina `sg`
- Permiso oficina `sm`
- Permiso oficina `nax`
- Permiso oficina `agd`
- Permiso oficina `est`

## Casos De Uso

- `src\personas\application\PersonasSelectData`

## Frontend Relacionado

- `frontend/personas/controller/personas_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.