---
id: "notas.nota_persona_form_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/nota_persona_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/nota_persona_form_data.php"
entrada: ["post.id_asignatura_real:string", "post.id_pau:integer", "post.mod:string", "post.pau:string", "post.sel:array"]
entrada_obligatoria: ["id_pau"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_NotaPersonaFormDataData"
respuesta_data: ["aOpcionesSituacion:array", "lista_situacion_no_acta:string", "vo:array", "NotaSituacion:array", "TipoActa:array", "NotaEpoca:array"]
requiere_hashb: false
errores: ["No se encuentra la nota a editar", "No se ha encontrado la asignatura con id: %s"]
frontend_referencias: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\NotaPersonaFormData"]
tags: ["notas", "nota", "persona", "form", "data"]
estado_revision: "revisado"
---

# Nota Persona Form Data

Payload del formulario de alta/edición de nota de persona.

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

## Objetivo funcional

Modos `mod` nuevo/editar; resuelve asignatura real, situaciones, URLs de mutación y opcionales.

## Permisos

- Dossier 1011.

## Errores conocidos

- `No se encuentra la nota a editar`
- `No se ha encontrado la asignatura con id: %s`

## Casos De Uso

- `src\notas\application\NotaPersonaFormData`

## Frontend Relacionado

- `frontend/notas/controller/form_notas_de_una_persona.php`.