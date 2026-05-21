---
id: "casas.prevision_asistentes_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/prevision_asistentes_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/prevision_asistentes_data.php"
entrada: ["post.fin_iso:string", "post.inicio_iso:string", "post.mi_of:string", "post.periodo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_PrevisionAsistentesDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "inicio_iso:string", "fin_iso:string", "inicio_local:string", "fin_local:string", "mi_of:string", "mi_sfsv:integer", "permitido:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/prevision_asistentes.php"]
casos_uso: ["src\\casas\\application\\PrevisionAsistentesData"]
tags: ["casas", "prevision", "asistentes", "data"]
estado_revision: "generado"
---

# Prevision Asistentes Data

Endpoint backend: datos de la pantalla `prevision_asistentes`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/prevision_asistentes_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/prevision_asistentes_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `fin_iso` | `string` | controller+application | No | controller+application |
| `inicio_iso` | `string` | controller+application | No | controller+application |
| `mi_of` | `string` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `casas_PrevisionAsistentesDataData`):
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)
  - `inicio_iso` (`string`)
  - `fin_iso` (`string`)
  - `inicio_local` (`string`)
  - `fin_local` (`string`)
  - `mi_of` (`string`)
  - `mi_sfsv` (`integer`)
  - `permitido` (`boolean`)

## Efectos colaterales

- Sucesor de la rama de listado de `apps/casas/controller/prevision_asistentes.php`.

## Casos De Uso

- `src\casas\application\PrevisionAsistentesData`

## Frontend Relacionado

- `frontend/casas/controller/prevision_asistentes.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.