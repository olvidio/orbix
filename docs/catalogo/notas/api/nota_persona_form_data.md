---
id: "notas.nota_persona_form_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/nota_persona_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/nota_persona_form_data.php"
entrada: ["post.id_asignatura_real:string", "post.id_pau:integer", "post.mod:string", "post.pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_NotaPersonaFormDataData"
respuesta_data: ["aOpcionesSituacion:array", "lista_situacion_no_acta:string", "vo:array", "NotaSituacion:array", "TipoActa:array", "NotaEpoca:array"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\NotaPersonaFormData"]
tags: ["notas", "nota", "persona", "form", "data"]
estado_revision: "generado"
---

# Nota Persona Form Data

Endpoint backend que prepara los datos para `form_notas_de_una_persona.phtml` (alta/edicion de `PersonaNota`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/nota_persona_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/nota_persona_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_asignatura_real` | `string` | controller+application | No | controller+application |
| `id_pau` | `integer` | controller+application | No | controller+application |
| `mod` | `string` | controller+application | No | controller+application |
| `pau` | `string` | controller+application | No | controller+application |
| `sel` | `array` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `notas_NotaPersonaFormDataData`):
  - `aOpcionesSituacion` (`array`)
  - `lista_situacion_no_acta` (`string`)
  - `vo` (`array`)
  - `NotaSituacion` (`array`)
  - `TipoActa` (`array`)
  - `NotaEpoca` (`array`)

## Casos De Uso

- `src\notas\application\NotaPersonaFormData`

## Frontend Relacionado

- `frontend/notas/controller/form_notas_de_una_persona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.