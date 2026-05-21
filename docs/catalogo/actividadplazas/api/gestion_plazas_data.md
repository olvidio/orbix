---
id: "actividadplazas.gestion_plazas_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/gestion_plazas_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_tipo_activ:string", "post.periodo:string", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_GestionPlazasDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "a_grupo:array", "extendida:boolean", "id_tipo_activ:string", "sactividad:string", "year:int|string", "periodo:string", "empiezamin:string", "empiezamax:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/gestion_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\GestionPlazasData"]
tags: ["actividadplazas", "gestion", "plazas", "data"]
estado_revision: "generado"
---

# Gestion Plazas Data

Endpoint backend: devuelve los datos del cuadro de gestion de plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo) para que el controller frontend monte el `frontend\shared\web\TablaEditable`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/gestion_plazas_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |
| `sactividad2` | `string` | controller+application | No | controller+application |
| `sasistentes` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_GestionPlazasDataData`):
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)
  - `a_grupo` (`array`)
  - `extendida` (`boolean`)
  - `id_tipo_activ` (`string`)
  - `sactividad` (`string`)
  - `year` (`int|string`)
  - `periodo` (`string`)
  - `empiezamin` (`string`)
  - `empiezamax` (`string`)

## Casos De Uso

- `src\actividadplazas\application\GestionPlazasData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/gestion_plazas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.