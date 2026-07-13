---
id: "notas.asig_faltan_personas_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asig_faltan_personas_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/asig_faltan_personas_select_data.php"
entrada: ["post.b_c:string", "post.c1:string", "post.c2:string", "post.id_asignatura:integer", "post.personas_agd:string", "post.personas_n:string"]
entrada_obligatoria: ["id_asignatura"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_AsigFaltanPersonasSelectTablaDataData"
respuesta_data: ["titulo:string, obj_pau:string, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, telfs:string, mails:string}>"]
requiere_hashb: false
errores: ["Debe marcar un grupo de personas (n o agd)", "No se ha encontrado la asignatura con id: %s"]
frontend_referencias: ["frontend/notas/controller/asig_faltan_personas_select.php"]
casos_uso: ["src\\notas\\application\\AsigFaltanPersonasSelectTablaData"]
tags: ["notas", "asig", "faltan", "personas", "select", "data"]
estado_revision: "revisado"
---

# Asig Faltan Personas Select Data

Lista personas a las que falta una asignatura concreta.

Tabla de `asig_faltan_personas_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asig_faltan_personas_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
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
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Tabla en `data` (doble `JSON.parse`).
- Payload en `data` (schema `notas_AsigFaltanPersonasSelectTablaDataData`):
  - `titulo` (`string, obj_pau:string, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, telfs:string, mails:string}>`)

## Objetivo funcional

Filtro por `id_asignatura` y grupo n/agd; título con nombre de asignatura y curso.

## Permisos

- Desde `asig_faltan_que`.

## Errores conocidos

- `Debe marcar un grupo de personas (n o agd)`
- `No se ha encontrado la asignatura con id: %s`

## Casos De Uso

- `src\notas\application\AsigFaltanPersonasSelectTablaData`

## Frontend Relacionado

- `frontend/notas/controller/asig_faltan_personas_select.php`.