---
id: "actividadestudios.form_matriculas_de_una_persona_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/form_matriculas_de_una_persona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/form_matriculas_de_una_persona_data.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_nom:integer", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_FormMatriculasDeUnaPersonaDataData"
respuesta_data: ["nom_activ:string", "mod:string", "id_asignatura_real:integer", "nombre_corto:string", "chk_preceptor:string", "id_preceptor:string|int", "oDesplProfesores_opciones:array", "oDesplNiveles_opciones:array", "condicion_js:string", "camposForm:string", "a_camposHidden:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php"]
casos_uso: ["src\\actividadestudios\\application\\FormMatriculasDeUnaPersonaData"]
tags: ["actividadestudios", "form", "matriculas", "de", "una", "persona", "data"]
estado_revision: "generado"
---

# Form Matriculas De Una Persona Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/form_matriculas_de_una_persona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/form_matriculas_de_una_persona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_asignatura` | `integer` | controller+application | No | controller+application |
| `id_nom` | `integer` | controller+application | No | controller+application |
| `sel` | `mixed` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_FormMatriculasDeUnaPersonaDataData`):
  - `nom_activ` (`string`)
  - `mod` (`string`)
  - `id_asignatura_real` (`integer`)
  - `nombre_corto` (`string`)
  - `chk_preceptor` (`string`)
  - `id_preceptor` (`string|int`)
  - `oDesplProfesores_opciones` (`array`)
  - `oDesplNiveles_opciones` (`array`)
  - `condicion_js` (`string`)
  - `camposForm` (`string`)
  - `a_camposHidden` (`array`)

## Casos De Uso

- `src\actividadestudios\application\FormMatriculasDeUnaPersonaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.