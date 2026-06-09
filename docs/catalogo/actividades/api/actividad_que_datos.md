---
id: "actividades.actividad_que_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_que_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_que_datos.php"
entrada: ["post.extendida:mixed", "post.id_tipo_activ:mixed", "post.para:string", "post.perm_jefe:mixed", "post.que:string", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string", "post.sfsv:string", "post.sfsv_all:mixed", "post.snom_tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadQueDatosData"
respuesta_data: ["actividad_tipo_html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"]
casos_uso: ["src\\actividades\\application\\ActividadQueDatos"]
tags: ["actividades", "actividad", "que", "datos"]
estado_revision: "generado"
---

# Actividad Que Datos

Endpoint backend: HTML del bloque tipo de actividad (desplegables) para actividad_que.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_que_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `extendida` | `mixed` | controller+application | No | controller+application |
| `id_tipo_activ` | `mixed` | controller+application | No | controller+application |
| `para` | `string` | controller+application | No | controller+application |
| `perm_jefe` | `mixed` | controller+application | No | controller+application |
| `que` | `string` | controller+application | No | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |
| `sactividad2` | `string` | controller+application | No | controller+application |
| `sasistentes` | `string` | controller+application | No | controller+application |
| `sfsv` | `string` | controller+application | No | controller+application |
| `sfsv_all` | `mixed` | controller+application | No | controller+application |
| `snom_tipo` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadQueDatosData`):
  - `actividad_tipo_html` (`string`)

## Casos De Uso

- `src\actividades\application\ActividadQueDatos`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.