---
id: "actividades.actividad_select_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_select_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_select_datos.php"
entrada: ["post.continuar:string", "post.dl_org:string", "post.empiezamax:string", "post.empiezamin:string", "post.fases_off:array", "post.fases_on:array", "post.filtro_lugar:string", "post.id_tipo_activ:string", "post.id_ubi:integer", "post.modo:string", "post.nom_activ:string", "post.periodo:string", "post.publicado:integer", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string", "post.scroll_id:string", "post.sel:array", "post.ssfsv:string", "post.stack_go:integer", "post.status:integer", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_select.php"]
casos_uso: ["src\\actividades\\application\\ActividadSelectListado"]
tags: ["actividades", "actividad", "select", "datos"]
estado_revision: "generado"
---

# Actividad Select Datos

JSON del listado para `actividad_select`: filtros POST → {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_select_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `continuar` | `string` | controller | No | controller |
| `dl_org` | `string` | controller | No | controller |
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `fases_off` | `array` | controller | No | controller |
| `fases_on` | `array` | controller | No | controller |
| `filtro_lugar` | `string` | controller | No | controller |
| `id_tipo_activ` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `modo` | `string` | controller | No | controller |
| `nom_activ` | `string` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `publicado` | `integer` | controller | No | controller |
| `sactividad` | `string` | controller | No | controller |
| `sactividad2` | `string` | controller | No | controller |
| `sasistentes` | `string` | controller | No | controller |
| `scroll_id` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |
| `ssfsv` | `string` | controller | No | controller |
| `stack_go` | `integer` | controller | No | controller |
| `status` | `integer` | controller | No | controller |
| `year` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ActividadSelectListado`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.