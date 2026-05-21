---
id: "notas.asignaturas_pendientes_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/asignaturas_pendientes_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/asignaturas_pendientes_data.php"
entrada: ["post.dl:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_AsignaturasPendientesDataData"
respuesta_data: ["cabeceras:array", "filas:array", "delegaciones:array", "ambito_rstgr:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/asignaturas_pendientes.php"]
casos_uso: ["src\\notas\\application\\AsignaturasPendientesData"]
tags: ["notas", "asignaturas", "pendientes", "data"]
estado_revision: "generado"
---

# Asignaturas Pendientes Data

Datos para la pantalla `asignaturas_pendientes` (matriz alumnos × asignaturas). La UI (`Lista`, desplegable rstgr) se monta en el controlador frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/asignaturas_pendientes_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/asignaturas_pendientes_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_AsignaturasPendientesDataData`):
  - `cabeceras` (`array`)
  - `filas` (`array`)
  - `delegaciones` (`array`)
  - `ambito_rstgr` (`boolean`)

## Casos De Uso

- `src\notas\application\AsignaturasPendientesData`

## Frontend Relacionado

- `frontend/notas/controller/asignaturas_pendientes.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.