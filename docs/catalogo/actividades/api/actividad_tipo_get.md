---
id: "actividades.actividad_tipo_get"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_tipo_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_tipo_get.php"
entrada: ["post.entrada:string", "post.extendida:string", "post.isfsv:integer", "post.modo:string", "post.opcion_sel:string", "post.salida:string", "post.ssfsv:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadTipoGetActividadData"
respuesta_data: ["id:string, opciones: array<int|string,string>, selected: string, blanco: bool, val_blanco: string, action: string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/helpers/ActividadTipo.php", "frontend/actividades/view/actividad_select_ubi.phtml", "frontend/pasarela/controller/nombre_form.php", "frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\actividades\\application\\ActividadTipoGetActividad", "src\\actividades\\application\\ActividadTipoGetAsistentes", "src\\actividades\\application\\ActividadTipoGetDlOrg", "src\\actividades\\application\\ActividadTipoGetFiltroLugar", "src\\actividades\\application\\ActividadTipoGetIdTarifa", "src\\actividades\\application\\ActividadTipoGetLugar", "src\\actividades\\application\\ActividadTipoGetNivelStgrDefecto", "src\\actividades\\application\\ActividadTipoGetNomTipo", "src\\actividades\\application\\ActividadTipoGetNomTipoTabla"]
tags: ["actividades", "actividad", "tipo", "get"]
estado_revision: "generado"
---

# Actividad Tipo Get

Endpoint backend que devuelve el payload necesario (datos de desplegable, tabla HTML o valor escalar) segun el parametro POST `salida`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_tipo_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_tipo_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `entrada` | `string` | application | No | application |
| `extendida` | `string` | application | No | application |
| `isfsv` | `integer` | application | No | application |
| `modo` | `string` | application | No | application |
| `opcion_sel` | `string` | application | No | application |
| `salida` | `string` | controller | No | controller |
| `ssfsv` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadTipoGetActividadData`):
  - `id` (`string, opciones: array<int|string,string>, selected: string, blanco: bool, val_blanco: string, action: string`)

## Efectos colaterales

- Devuelve el payload (id, opciones, selected, blanco, val_blanco, action) del desplegable de asistentes posibles.

## Permisos

- Permiso oficina `des`
- Permiso oficina `calendario`

## Casos De Uso

- `src\actividades\application\ActividadTipoGetActividad`
- `src\actividades\application\ActividadTipoGetAsistentes`
- `src\actividades\application\ActividadTipoGetDlOrg`
- `src\actividades\application\ActividadTipoGetFiltroLugar`
- `src\actividades\application\ActividadTipoGetIdTarifa`
- `src\actividades\application\ActividadTipoGetLugar`
- `src\actividades\application\ActividadTipoGetNivelStgrDefecto`
- `src\actividades\application\ActividadTipoGetNomTipo`
- `src\actividades\application\ActividadTipoGetNomTipoTabla`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/helpers/ActividadTipo.php`
- `frontend/actividades/view/actividad_select_ubi.phtml`
- `frontend/pasarela/controller/nombre_form.php`
- `frontend/procesos/controller/fases_activ_cambio.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.