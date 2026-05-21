---
id: "asistentes.form_actividades_de_una_persona_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/form_actividades_de_una_persona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/form_actividades_de_una_persona_data.php"
entrada: ["post.id_pau:integer", "post.id_tipo:string", "post.obj_pau:string", "post.que_dl:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/form_actividades_de_una_persona.php"]
casos_uso: ["src\\asistentes\\application\\FormActividadesDeUnaPersonaData"]
tags: ["asistentes", "form", "actividades", "de", "una", "persona", "data"]
estado_revision: "generado"
---

# Form Actividades De Una Persona Data

Dossier actividades de una persona (1301). Datos puros para el formulario; la UI (HashFront, Desplegable) se compone en frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/form_actividades_de_una_persona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/form_actividades_de_una_persona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_pau` | `integer` | application | No | application |
| `id_tipo` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `que_dl` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Dossier actividades de una persona (1301).

## Casos De Uso

- `src\asistentes\application\FormActividadesDeUnaPersonaData`

## Frontend Relacionado

- `frontend/asistentes/controller/form_actividades_de_una_persona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.