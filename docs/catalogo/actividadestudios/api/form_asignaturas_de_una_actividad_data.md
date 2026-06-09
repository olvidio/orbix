---
id: "actividadestudios.form_asignaturas_de_una_actividad_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/form_asignaturas_de_una_actividad_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/form_asignaturas_de_una_actividad_data.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_pau:integer", "post.pau:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_FormAsignaturasDeUnaActividadDataData"
respuesta_data: ["mod:string", "id_activ:integer", "id_asignatura:integer", "nombre_corto:string", "chk_avisado:string", "chk_confirmado:string", "chk_preceptor:string", "f_ini:string", "f_fin:string", "oDesplProfesores_opciones:array", "id_profesor_sel:int|string", "oDesplAsignaturas_opciones:array", "primary_key_s:string", "camposForm:string", "a_camposHidden:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\FormAsignaturasDeUnaActividadData"]
tags: ["actividadestudios", "form", "asignaturas", "de", "una", "actividad", "data"]
estado_revision: "generado"
---

# Form Asignaturas De Una Actividad Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/form_asignaturas_de_una_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/form_asignaturas_de_una_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_asignatura` | `integer` | controller+application | No | controller+application |
| `id_pau` | `integer` | controller+application | No | controller+application |
| `pau` | `string` | controller+application | No | controller+application |
| `sel` | `mixed` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_FormAsignaturasDeUnaActividadDataData`):
  - `mod` (`string`)
  - `id_activ` (`integer`)
  - `id_asignatura` (`integer`)
  - `nombre_corto` (`string`)
  - `chk_avisado` (`string`)
  - `chk_confirmado` (`string`)
  - `chk_preceptor` (`string`)
  - `f_ini` (`string`)
  - `f_fin` (`string`)
  - `oDesplProfesores_opciones` (`array`)
  - `id_profesor_sel` (`int|string`)
  - `oDesplAsignaturas_opciones` (`array`)
  - `primary_key_s` (`string`)
  - `camposForm` (`string`)
  - `a_camposHidden` (`array`)

## Casos De Uso

- `src\actividadestudios\application\FormAsignaturasDeUnaActividadData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.