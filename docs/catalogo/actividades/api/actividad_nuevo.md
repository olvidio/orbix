---
id: "actividades.actividad_nuevo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_nuevo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_nuevo.php"
entrada: ["post.desc_activ:string", "post.dl_org:string", "post.f_fin:string", "post.f_ini:string", "post.h_fin:string", "post.h_ini:string", "post.id_repeticion:integer", "post.id_tarifa:integer", "post.id_tipo_activ:integer", "post.id_ubi:integer", "post.idioma:string", "post.inom_tipo_val:string", "post.lugar_esp:string", "post.nivel_stgr:string", "post.nom_activ:string", "post.num_asistentes:integer", "post.observ:string", "post.observ_material:string", "post.plazas:integer", "post.precio:mixed", "post.publicado:string", "post.status:integer", "post.tipo_horario:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_nuevo_curso.php"]
casos_uso: ["src\\actividades\\application\\ActividadNueva"]
tags: ["actividades", "actividad", "nuevo"]
estado_revision: "generado"
---

# Actividad Nuevo

Endpoint backend AJAX: crea una nueva actividad a partir de los datos del formulario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_nuevo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_activ` | `string` | controller | No | controller |
| `dl_org` | `string` | controller | No | controller |
| `f_fin` | `string` | controller | No | controller |
| `f_ini` | `string` | controller | No | controller |
| `h_fin` | `string` | controller | No | controller |
| `h_ini` | `string` | controller | No | controller |
| `id_repeticion` | `integer` | controller | No | controller |
| `id_tarifa` | `integer` | controller | No | controller |
| `id_tipo_activ` | `integer` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `idioma` | `string` | controller | No | controller |
| `inom_tipo_val` | `string` | controller | No | controller |
| `lugar_esp` | `string` | controller | No | controller |
| `nivel_stgr` | `string` | controller | No | controller |
| `nom_activ` | `string` | controller | No | controller |
| `num_asistentes` | `integer` | controller | No | controller |
| `observ` | `string` | controller | No | controller |
| `observ_material` | `string` | controller | No | controller |
| `plazas` | `integer` | controller | No | controller |
| `precio` | `mixed` | controller | No | controller |
| `publicado` | `string` | controller | No | controller |
| `status` | `integer` | controller | No | controller |
| `tipo_horario` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ActividadNueva`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_nuevo_curso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.