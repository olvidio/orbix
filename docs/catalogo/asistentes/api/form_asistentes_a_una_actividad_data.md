---
id: "asistentes.form_asistentes_a_una_actividad_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/form_asistentes_a_una_actividad_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/form_asistentes_a_una_actividad_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.na:string", "post.obj_pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/form_asistentes_a_una_actividad.php"]
casos_uso: ["src\\asistentes\\application\\FormAsistentesAUnaActividadData"]
tags: ["asistentes", "form", "a", "una", "actividad", "data"]
estado_revision: "generado"
---

# Form Asistentes A Una Actividad Data

Dossier asistentes a una actividad (3101). Datos puros; la UI vive en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/form_asistentes_a_una_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/form_asistentes_a_una_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `na` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Dossier asistentes a una actividad (3101).
- Datos puros; la UI vive en {@see \frontend\asistentes\helpers\FormAsistentesAUnaActividadRender}.

## Casos De Uso

- `src\asistentes\application\FormAsistentesAUnaActividadData`

## Frontend Relacionado

- `frontend/asistentes/controller/form_asistentes_a_una_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.