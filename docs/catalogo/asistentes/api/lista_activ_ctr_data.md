---
id: "asistentes.lista_activ_ctr_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_activ_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_activ_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_ubi:integer", "post.n_agd:string", "post.periodo:string", "post.sactividad:string", "post.sasistentes:string", "post.ssfsv:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaActivCtrDataData"
respuesta_data: ["aCentros:array"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_activ_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaActivCtrData"]
tags: ["asistentes", "lista", "activ", "ctr", "data"]
estado_revision: "generado"
---

# Lista Activ Ctr Data

Asistentes a actividades por centro (`lista_activ_ctr.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/lista_activ_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_activ_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `n_agd` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `sactividad` | `string` | application | No | application |
| `sasistentes` | `string` | application | No | application |
| `ssfsv` | `string` | application | No | application |
| `year` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asistentes_ListaActivCtrDataData`):
  - `aCentros` (`array`)

## Efectos colaterales

- Asistentes a actividades por centro (`lista_activ_ctr.php`).

## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

## Casos De Uso

- `src\asistentes\application\ListaActivCtrData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_activ_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.