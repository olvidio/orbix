---
id: "actividades.actividad_editar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_editar.php"
entrada: ["post.desc_activ:string", "post.dl_org:string", "post.f_fin:string", "post.f_ini:string", "post.h_fin:string", "post.h_ini:string", "post.iactividad_val:integer", "post.iasistentes_val:integer", "post.id_activ:integer", "post.id_repeticion:integer", "post.id_tarifa:integer", "post.id_tipo_activ:integer", "post.id_ubi:integer", "post.idioma:string", "post.inom_tipo_val:string", "post.isfsv_val:integer", "post.lugar_esp:string", "post.nivel_stgr:integer", "post.nom_activ:string", "post.num_asistentes:integer", "post.observ:string", "post.observ_material:string", "post.plazas:integer", "post.precio:mixed", "post.publicado:string", "post.status:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadEditarData"
respuesta_data: ["error_txt:string, tipo_error?: string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\ActividadEditar"]
tags: ["actividades", "actividad", "editar"]
estado_revision: "generado"
---

# Actividad Editar

Endpoint backend AJAX: guarda la edicion de una actividad existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_activ` | `string` | application | No | application |
| `dl_org` | `string` | application | No | application |
| `f_fin` | `string` | application | No | application |
| `f_ini` | `string` | application | No | application |
| `h_fin` | `string` | application | No | application |
| `h_ini` | `string` | application | No | application |
| `iactividad_val` | `integer` | application | No | application |
| `iasistentes_val` | `integer` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_repeticion` | `integer` | application | No | application |
| `id_tarifa` | `integer` | application | No | application |
| `id_tipo_activ` | `integer` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `idioma` | `string` | application | No | application |
| `inom_tipo_val` | `string` | application | No | application |
| `isfsv_val` | `integer` | application | No | application |
| `lugar_esp` | `string` | application | No | application |
| `nivel_stgr` | `integer` | application | No | application |
| `nom_activ` | `string` | application | No | application |
| `num_asistentes` | `integer` | application | No | application |
| `observ` | `string` | application | No | application |
| `observ_material` | `string` | application | No | application |
| `plazas` | `integer` | application | No | application |
| `precio` | `mixed` | application | No | application |
| `publicado` | `string` | application | No | application |
| `status` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadEditarData`):
  - `error_txt` (`string, tipo_error?: string`)

## Casos De Uso

- `src\actividades\application\ActividadEditar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.