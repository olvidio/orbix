---
id: "procesos.fases_activ_cambio_tipo_html"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_tipo_html"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_tipo_html.php"
entrada: ["post.id_tipo_activ:string", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_FasesActivCambioTipoActividadHtmlDataData"
respuesta_data: ["tipo_actividad_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioTipoActividadHtmlData"]
tags: ["procesos", "fases", "activ", "cambio", "tipo", "html"]
estado_revision: "generado"
---

# Fases Activ Cambio Tipo Html

Payload para fases_activ_cambio: HTML del selector tipo actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_tipo_html`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_tipo_html.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | application | No | application |
| `sactividad` | `string` | application | No | application |
| `sactividad2` | `string` | application | No | application |
| `sasistentes` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_FasesActivCambioTipoActividadHtmlDataData`):
  - `tipo_actividad_html` (`string`)

## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`
- Permiso oficina `calendario`

## Casos De Uso

- `src\procesos\application\FasesActivCambioTipoActividadHtmlData`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.