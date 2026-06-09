---
id: "asistentes.lista_asis_conjunto_activ_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_asis_conjunto_activ_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_asis_conjunto_activ_data.php"
entrada: ["post.dl_org:string", "post.empiezamax:string", "post.empiezamin:string", "post.id_tipo_activ:string", "post.id_ubi:integer", "post.modo:string", "post.nom_activ:string", "post.periodo:string", "post.que:string", "post.sactividad:string", "post.sasistentes:string", "post.sfsv:string", "post.snom_tipo:string", "post.status:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaAsisConjuntoActivDataData"
respuesta_data: ["content_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_asis_conjunto_activ.php"]
casos_uso: ["src\\asistentes\\application\\ListaAsisConjuntoActivData"]
tags: ["asistentes", "lista", "asis", "conjunto", "activ", "data"]
estado_revision: "generado"
---

# Lista Asis Conjunto Activ Data

Listados conjuntos de plazas/actividades (`lista_asis_conjunto_activ.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/lista_asis_conjunto_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_asis_conjunto_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_org` | `string` | application | No | application |
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `id_tipo_activ` | `string` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `modo` | `string` | application | No | application |
| `nom_activ` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `que` | `string` | application | No | application |
| `sactividad` | `string` | application | No | application |
| `sasistentes` | `string` | application | No | application |
| `sfsv` | `string` | application | No | application |
| `snom_tipo` | `string` | application | No | application |
| `status` | `integer` | application | No | application |
| `year` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asistentes_ListaAsisConjuntoActivDataData`):
  - `content_html` (`string`)

## Casos De Uso

- `src\asistentes\application\ListaAsisConjuntoActivData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_asis_conjunto_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.