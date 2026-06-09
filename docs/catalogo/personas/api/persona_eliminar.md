---
id: "personas.persona_eliminar"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/persona_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/persona_eliminar.php"
entrada: ["post.id_nom:integer", "post.obj_pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha pasado el id_nom", "No existe la clase de la persona", "No se encuentra la persona", "No se ha eliminado, porque no es de mi dl"]
frontend_referencias: ["frontend/personas/view/_persona_form_js.phtml"]
casos_uso: ["src\\personas\\application\\PersonaEliminar"]
tags: ["personas", "persona", "eliminar"]
estado_revision: "generado"
---

# Persona Eliminar

Endpoint JSON: elimina una persona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/persona_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina una persona si pertenece a la dl del usuario actual.
- Migrado desde la rama "eliminar" de `apps/personas/controller/personas_update.php` (slice 2 de la migracion del modulo `personas`).

## Errores conocidos

- `No se ha pasado el id_nom`
- `No existe la clase de la persona`
- `No se encuentra la persona`
- `No se ha eliminado, porque no es de mi dl`

## Casos De Uso

- `src\personas\application\PersonaEliminar`

## Frontend Relacionado

- `frontend/personas/view/_persona_form_js.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.