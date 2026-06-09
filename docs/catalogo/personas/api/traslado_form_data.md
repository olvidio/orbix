---
id: "personas.traslado_form_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/traslado_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/personas/infrastructure/ui/http/controllers/traslado_form_data.php"
entrada: ["post.id_pau:integer", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/personas/controller/traslado_form.php"]
casos_uso: ["src\\personas\\application\\TrasladoFormData"]
tags: ["personas", "traslado", "form", "data"]
estado_revision: "generado"
---

# Traslado Form Data

Endpoint JSON: datos para el formulario de traslado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/traslado_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/traslado_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_pau` | `integer` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\personas\application\TrasladoFormData`

## Frontend Relacionado

- `frontend/personas/controller/traslado_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.