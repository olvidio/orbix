---
id: "notas.asig_faltan_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asig_faltan_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/asig_faltan_select_data.php"
entrada: ["post.b_c:string", "post.c1:string", "post.c2:string", "post.lista:string", "post.numero:integer", "post.personas_agd:string", "post.personas_n:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_AsigFaltanSelectTablaDataData"
respuesta_data: ["titulo:string, obj_pau:string, lista:bool, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, asig_txt:string|int, telfs:string, mails:string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/asig_faltan_select.php"]
casos_uso: ["src\\notas\\application\\AsigFaltanSelectTablaData"]
tags: ["notas", "asig", "faltan", "select", "data"]
estado_revision: "generado"
---

# Asig Faltan Select Data

Tabla de `asig_faltan_select` (asignaturas pendientes por persona).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asig_faltan_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/asig_faltan_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `b_c` | `string` | controller | No | controller |
| `c1` | `string` | controller | No | controller |
| `c2` | `string` | controller | No | controller |
| `lista` | `string` | controller | No | controller |
| `numero` | `integer` | controller | No | controller |
| `personas_agd` | `string` | controller | No | controller |
| `personas_n` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_AsigFaltanSelectTablaDataData`):
  - `titulo` (`string, obj_pau:string, lista:bool, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, asig_txt:string|int, telfs:string, mails:string}>`)

## Casos De Uso

- `src\notas\application\AsigFaltanSelectTablaData`

## Frontend Relacionado

- `frontend/notas/controller/asig_faltan_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.