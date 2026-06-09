---
id: "notas.asig_faltan_personas_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asig_faltan_personas_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/asig_faltan_personas_select_data.php"
entrada: ["post.b_c:string", "post.c1:string", "post.c2:string", "post.id_asignatura:integer", "post.personas_agd:string", "post.personas_n:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_AsigFaltanPersonasSelectTablaDataData"
respuesta_data: ["titulo:string, obj_pau:string, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, telfs:string, mails:string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/asig_faltan_personas_select.php"]
casos_uso: ["src\\notas\\application\\AsigFaltanPersonasSelectTablaData"]
tags: ["notas", "asig", "faltan", "personas", "select", "data"]
estado_revision: "generado"
---

# Asig Faltan Personas Select Data

Tabla de `asig_faltan_personas_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asig_faltan_personas_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/asig_faltan_personas_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `b_c` | `string` | controller | No | controller |
| `c1` | `string` | controller | No | controller |
| `c2` | `string` | controller | No | controller |
| `id_asignatura` | `integer` | controller | No | controller |
| `personas_agd` | `string` | controller | No | controller |
| `personas_n` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_AsigFaltanPersonasSelectTablaDataData`):
  - `titulo` (`string, obj_pau:string, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, telfs:string, mails:string}>`)

## Casos De Uso

- `src\notas\application\AsigFaltanPersonasSelectTablaData`

## Frontend Relacionado

- `frontend/notas/controller/asig_faltan_personas_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.