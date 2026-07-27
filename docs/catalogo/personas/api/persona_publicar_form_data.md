---
id: "personas.persona_publicar_form_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/persona_publicar_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/personas/infrastructure/ui/http/controllers/persona_publicar_form_data.php"
entrada: ["post.id_nom:integer", "post.id_tabla:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra la persona", "No se puede determinar el esquema de la persona"]
frontend_referencias: ["frontend/personas/controller/persona_publicar_form.php"]
casos_uso: ["src\\personas\\application\\PersonaPublicarFormData"]
tags: ["personas", "persona", "publicar", "form", "data"]
estado_revision: "revisado"
---

# Persona Publicar Form Data

Datos del formulario para publicar una persona hacia otras delegaciones (caso B: visible en
desplegables de la DL destino con TTL).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve la persona por `sel` (`id_nom#id_tabla`) o por `id_nom` + `id_tabla`, obtiene nombre e
`id_schema`, y construye `opciones_dl` con las DL que tienen esquema en Orbix, excluyendo la propia
(`ConfigGlobal::mi_dele()`). Códigos normalizados sin sufijo v/f (`PersonaPublicacion::normalizarDl`).

## Endpoint

- URL: `/src/personas/persona_publicar_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_publicar_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | application | No | Preferente: token `id_nom#id_tabla` (array o string) |
| `id_nom` | `integer` | application | Cond. | Si no hay `sel` |
| `id_tabla` | `string` | application | No | Tabla del colectivo; si falta, se infiere del hallazgo global |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito (`data`): `nom`, `id_nom`, `id_tabla`, `id_schema`, `opciones_dl` (mapa código→código).
- Error: `ContestarJson::enviar(error)` sin payload útil.

## Casos particulares

- Con `sel` → toma el primer token; `id_nom` = antes de `#`, `id_tabla` = después.
- Si `id_tabla` resuelve repositorio y encuentra persona → usa ese nombre/`id_schema`.
- Si no (tabla inválida o no encontrada) → fallback `PersonaFinderService::findPersonaEnGlobal`.
- `id_schema < 1` tras la búsqueda → error de esquema.
- `opciones_dl` no incluye `*` (todas las DL) ni la DL propia; orden natural.

## Permisos

- Sin control en el caso de uso. El botón «publicar» del listado solo aparece con
  `have_perm_oficina` `est`, `sm` o `agd`.

## Errores conocidos

- `No se encuentra la persona`
- `No se puede determinar el esquema de la persona`

## Casos De Uso

- `src\personas\application\PersonaPublicarFormData`

## Frontend Relacionado

- `frontend/personas/controller/persona_publicar_form.php` (desde listado `fnjs_publicar`)
- Vista: `frontend/personas/view/persona_publicar.phtml`
