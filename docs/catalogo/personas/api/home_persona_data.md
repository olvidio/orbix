---
id: "personas.home_persona_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/home_persona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/home_persona_data.php"
entrada: ["post.obj_pau:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/personas/controller/home_persona.php"]
casos_uso: ["src\\personas\\application\\HomePersonaData"]
tags: ["personas", "home", "persona", "data"]
estado_revision: "generado"
---

# Home Persona Data

Endpoint JSON: datos para la pantalla `home_persona.phtml`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/home_persona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/home_persona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\personas\application\HomePersonaData`

## Frontend Relacionado

- `frontend/personas/controller/home_persona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.