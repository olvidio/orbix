---
id: "actividades.lista_activ_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_activ_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_activ_datos.php"
entrada: ["post.asist:array", "post.c_activ:array", "post.dl_org:string", "post.empiezamax:string", "post.empiezamin:string", "post.filtro_lugar:string", "post.id_tipo_activ:string", "post.id_ubi:integer", "post.periodo:string", "post.que:string", "post.sactividad:string", "post.sasistentes:string", "post.seccion:array", "post.snom_tipo:string", "post.ssfsv:string", "post.status:mixed", "post.titulo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ListaActivTablaData"
respuesta_data: ["titulo:string", "ver_hora:integer", "ver_tarifa:integer", "ver_sacd:integer", "a_cabeceras:list<array<string, mixed>|string>", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/lista_activ.php", "frontend/actividades/controller/lista_activ_que.php"]
casos_uso: ["src\\actividades\\application\\ListaActivTabla"]
tags: ["actividades", "lista", "activ", "datos"]
estado_revision: "generado"
---

# Lista Activ Datos

JSON del listado `lista_activ`: filtros POST → {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/lista_activ_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_activ_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `asist` | `array` | controller+application | No | controller+application |
| `c_activ` | `array` | controller+application | No | controller+application |
| `dl_org` | `string` | controller+application | No | controller+application |
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `filtro_lugar` | `string` | controller | No | controller |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `que` | `string` | controller+application | No | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |
| `sasistentes` | `string` | controller+application | No | controller+application |
| `seccion` | `array` | controller+application | No | controller+application |
| `snom_tipo` | `string` | controller+application | No | controller+application |
| `ssfsv` | `string` | controller+application | No | controller+application |
| `status` | `mixed` | controller+application | No | controller+application |
| `titulo` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ListaActivTablaData`):
  - `titulo` (`string`)
  - `ver_hora` (`integer`)
  - `ver_tarifa` (`integer`)
  - `ver_sacd` (`integer`)
  - `a_cabeceras` (`list<array<string, mixed>|string>`)
  - `a_valores` (`array`)

## Casos De Uso

- `src\actividades\application\ListaActivTabla`

## Frontend Relacionado

- `frontend/actividades/controller/lista_activ.php`
- `frontend/actividades/controller/lista_activ_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.