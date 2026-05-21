---
id: "actividades.actividad_cambiar_tipo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_cambiar_tipo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_cambiar_tipo.php"
entrada: ["post.desc_activ:string", "post.dl_org:string", "post.f_fin:string", "post.f_ini:string", "post.h_fin:string", "post.h_ini:string", "post.iactividad_val:integer", "post.iasistentes_val:integer", "post.id_activ:integer", "post.id_repeticion:integer", "post.id_tarifa:integer", "post.id_tipo_activ:integer", "post.id_ubi:integer", "post.inom_tipo_val:string", "post.isfsv_val:integer", "post.lugar_esp:string", "post.nivel_stgr:integer", "post.nom_activ:string", "post.num_asistentes:integer", "post.observ:string", "post.observ_material:string", "post.plazas:integer", "post.precio:mixed", "post.status:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["actividades", "actividad", "cambiar", "tipo"]
estado_revision: "generado"
---

# Actividad Cambiar Tipo

Endpoint backend AJAX: cambia el tipo de una actividad existente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_cambiar_tipo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_cambiar_tipo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_activ` | `string` | controller | No | controller |
| `dl_org` | `string` | controller | No | controller |
| `f_fin` | `string` | controller | No | controller |
| `f_ini` | `string` | controller | No | controller |
| `h_fin` | `string` | controller | No | controller |
| `h_ini` | `string` | controller | No | controller |
| `iactividad_val` | `integer` | controller | No | controller |
| `iasistentes_val` | `integer` | controller | No | controller |
| `id_activ` | `integer` | controller | No | controller |
| `id_repeticion` | `integer` | controller | No | controller |
| `id_tarifa` | `integer` | controller | No | controller |
| `id_tipo_activ` | `integer` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `inom_tipo_val` | `string` | controller | No | controller |
| `isfsv_val` | `integer` | controller | No | controller |
| `lugar_esp` | `string` | controller | No | controller |
| `nivel_stgr` | `integer` | controller | No | controller |
| `nom_activ` | `string` | controller | No | controller |
| `num_asistentes` | `integer` | controller | No | controller |
| `observ` | `string` | controller | No | controller |
| `observ_material` | `string` | controller | No | controller |
| `plazas` | `integer` | controller | No | controller |
| `precio` | `mixed` | controller | No | controller |
| `status` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.