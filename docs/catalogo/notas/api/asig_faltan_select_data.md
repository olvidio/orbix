---
id: "notas.asig_faltan_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asig_faltan_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/asig_faltan_select_data.php"
entrada: ["post.b_c:string", "post.c1:string", "post.c2:string", "post.lista:string", "post.numero:integer", "post.personas_agd:string", "post.personas_n:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_AsigFaltanSelectTablaDataData"
respuesta_data: ["titulo:string, obj_pau:string, lista:bool, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, asig_txt:string|int, telfs:string, mails:string}>"]
requiere_hashb: false
errores: ["Debe marcar un grupo de personas (n o agd)"]
frontend_referencias: ["frontend/notas/controller/asig_faltan_select.php"]
casos_uso: ["src\\notas\\application\\AsigFaltanSelectTablaData"]
tags: ["notas", "asig", "faltan", "select", "data"]
estado_revision: "revisado"
---

# Asig Faltan Select Data

Tabla de personas con asignaturas pendientes (filtro por número máximo).

Tabla de `asig_faltan_select` (asignaturas pendientes por persona).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asig_faltan_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
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
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Payload tabla (`titulo`, filas, cabeceras) en `data`.
- Payload en `data` (schema `notas_AsigFaltanSelectTablaDataData`):
  - `titulo` (`string, obj_pau:string, lista:bool, rows: list<array{id_nom:int, id_tabla:string, nom:string, nombre_ubi:string, stgr:string, asig_txt:string|int, telfs:string, mails:string}>`)

## Objetivo funcional

Lista alumnos n/agd que les faltan ≤ `numero` asignaturas para finalizar bienio/cuadrienio. Requiere marcar `personas_n` o `personas_agd`.

## Permisos

- Menú buscar asig. pendientes.

## Errores conocidos

- `Debe marcar un grupo de personas (n o agd)`

## Casos De Uso

- `src\notas\application\AsigFaltanSelectTablaData`

## Frontend Relacionado

- `frontend/notas/controller/asig_faltan_select.php` (desde `asig_faltan_que`).