---
id: "personas.persona_update"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/persona_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/persona_update.php"
entrada: ["post.apel_fam:string", "post.apellido1:string", "post.apellido2:string", "post.ce:integer", "post.ce_fin:integer", "post.ce_ini:integer", "post.ce_lugar:string", "post.dl:string", "post.eap:string", "post.edad:string", "post.f_inc:string", "post.f_nacimiento:string", "post.f_situacion:string", "post.id_ctr:integer", "post.id_nom:integer", "post.idioma_preferido:string", "post.inc:string", "post.lugar_nacimiento:string", "post.nivel_stgr:integer", "post.nom:string", "post.nx1:string", "post.nx2:string", "post.obj_pau:string", "post.observ:string", "post.profesion:string", "post.profesor_stgr:string", "post.sacd:string", "post.situacion:string", "post.trato:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha pasado el id_nom", "No existe la clase de la persona"]
frontend_referencias: ["frontend/personas/view/_persona_form_js.phtml"]
casos_uso: ["src\\personas\\application\\PersonaUpdate"]
tags: ["personas", "persona", "update"]
estado_revision: "generado"
---

# Persona Update

Endpoint JSON: guarda los datos de una persona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/persona_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `apel_fam` | `string` | application | No | application |
| `apellido1` | `string` | application | No | application |
| `apellido2` | `string` | application | No | application |
| `ce` | `integer` | application | No | application |
| `ce_fin` | `integer` | application | No | application |
| `ce_ini` | `integer` | application | No | application |
| `ce_lugar` | `string` | application | No | application |
| `dl` | `string` | application | No | application |
| `eap` | `string` | application | No | application |
| `edad` | `string` | application | No | application |
| `f_inc` | `string` | application | No | application |
| `f_nacimiento` | `string` | application | No | application |
| `f_situacion` | `string` | application | No | application |
| `id_ctr` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `idioma_preferido` | `string` | application | No | application |
| `inc` | `string` | application | No | application |
| `lugar_nacimiento` | `string` | application | No | application |
| `nivel_stgr` | `integer` | application | No | application |
| `nom` | `string` | application | No | application |
| `nx1` | `string` | application | No | application |
| `nx2` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `observ` | `string` | application | No | application |
| `profesion` | `string` | application | No | application |
| `profesor_stgr` | `string` | application | No | application |
| `sacd` | `string` | application | No | application |
| `situacion` | `string` | application | No | application |
| `trato` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `No se ha pasado el id_nom`
- `No existe la clase de la persona`

## Casos De Uso

- `src\personas\application\PersonaUpdate`

## Frontend Relacionado

- `frontend/personas/view/_persona_form_js.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.