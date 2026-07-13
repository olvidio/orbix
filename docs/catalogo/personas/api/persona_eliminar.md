---
id: "personas.persona_eliminar"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/persona_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/persona_eliminar.php"
entrada: ["post.id_nom:integer", "post.obj_pau:string"]
entrada_obligatoria: ["post.id_nom", "post.obj_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha pasado el id_nom", "No existe la clase de la persona", "No se encuentra la persona", "No se ha eliminado, porque no es de mi dl", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/personas/view/_persona_form_js.phtml"]
casos_uso: ["src\\personas\\application\\PersonaEliminar"]
tags: ["personas", "persona", "eliminar"]
estado_revision: "revisado"
---

# Persona Eliminar

Elimina una persona del colectivo indicado, solo si pertenece a la delegación del usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve repositorio por `obj_pau` (N, Agd, Nax, S, SSSC, Ex, Sacd). Comprueba
`ConfigGlobal::mi_delef() === persona.getDl()` antes de `Eliminar`. Linaje:
`apps/personas/controller/personas_update.php` (rama eliminar).

## Endpoint

- URL: `/src/personas/persona_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Sí | |
| `obj_pau` | `string` | controller | Sí | Incluye `PersonaSacd` |

## Salida

- Helper: `ContestarJson::enviar($error_txt, 'ok')`.
- Éxito: `data: "ok"`. Error en `mensaje`.

## Permisos

- Implícito: solo personas de `mi_delef`. Botón eliminar en ficha solo si `ok=1` (permiso oficina).

## Errores conocidos

- `No se ha pasado el id_nom`
- `No existe la clase de la persona`
- `No se encuentra la persona`
- `No se ha eliminado, porque no es de mi dl`
- `hay un error, no se ha eliminado` (+ detalle repositorio)

## Casos De Uso

- `src\personas\application\PersonaEliminar`

## Frontend Relacionado

- `frontend/personas/view/_persona_form_js.phtml` (botón eliminar en ficha)
