---
id: "notas.acta_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_select_data.php"
entrada: ["post.acta:mixed", "post.mes_fin_stgr:mixed", "post.titulo:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_ActaSelectDataData"
respuesta_data: ["titulo:string, a_asignaturas: array<int|string, string|null>, actas: list<array{acta:string, f_acta:?string, id_asignatura:int, has_pdf:bool}>"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_select.php"]
casos_uso: ["src\\notas\\application\\ActaSelectData"]
tags: ["notas", "acta", "select", "data"]
estado_revision: "generado"
---

# Acta Select Data

Lista de actas y mapa de asignaturas para `acta_select` (frontend sin repositorios).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `mixed` | controller | No | controller |
| `mes_fin_stgr` | `mixed` | controller | No | controller |
| `titulo` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_ActaSelectDataData`):
  - `titulo` (`string, a_asignaturas: array<int|string, string|null>, actas: list<array{acta:string, f_acta:?string, id_asignatura:int, has_pdf:bool}>`)

## Casos De Uso

- `src\notas\application\ActaSelectData`

## Frontend Relacionado

- `frontend/notas/controller/acta_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.