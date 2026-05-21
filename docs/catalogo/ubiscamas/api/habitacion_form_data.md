---
id: "ubiscamas.habitacion_form_data"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/habitacion_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/habitacion_form_data.php"
entrada: ["post.id_habitacion:string", "post.id_ubi:integer", "post.nuevo:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/controller/habitacion_form.php"]
casos_uso: ["src\\ubiscamas\\application\\HabitacionFormData"]
tags: ["ubiscamas", "habitacion", "form", "data"]
estado_revision: "generado"
---

# Habitacion Form Data

Datos para `frontend/ubiscamas/controller/habitacion_form.php`. La composición de `HashFront` ocurre en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/habitacion_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_habitacion` | `string` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `nuevo` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\ubiscamas\application\HabitacionFormData`

## Frontend Relacionado

- `frontend/ubiscamas/controller/habitacion_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.