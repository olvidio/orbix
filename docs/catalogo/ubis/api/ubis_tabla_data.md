---
id: "ubis.ubis_tabla_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_tabla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_tabla_data.php"
entrada: ["post.error:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisTablaDataData"
respuesta_data: ["0:string|list<string>, 1: string, 2: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_tabla.php"]
casos_uso: ["src\\ubis\\application\\UbisTablaData"]
tags: ["ubis", "tabla", "data"]
estado_revision: "generado"
---

# Ubis Tabla Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_tabla_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_tabla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `error` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_UbisTablaDataData`):
  - `0` (`string|list<string>, 1: string, 2: array<string, mixed>`)

## Permisos

- Permiso oficina `scl`
- Permiso oficina `vcsd`
- Permiso oficina `des`

## Casos De Uso

- `src\ubis\application\UbisTablaData`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_tabla.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.