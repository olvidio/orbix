---
id: "asistentes.lista_est_ctr_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_est_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_est_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_ubi:integer", "post.n_agd:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaEstCtrDataData"
respuesta_data: ["lista_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_est_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaEstCtrData"]
tags: ["asistentes", "lista", "est", "ctr", "data"]
estado_revision: "generado"
---

# Lista Est Ctr Data

Listado estudios por centro (`lista_est_ctr.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/lista_est_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_est_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `n_agd` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `year` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asistentes_ListaEstCtrDataData`):
  - `lista_html` (`string`)

## Casos De Uso

- `src\asistentes\application\ListaEstCtrData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_est_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.