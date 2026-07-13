---
id: "personas.personas_editar_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/personas_editar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/personas/infrastructure/ui/http/controllers/personas_editar_data.php"
entrada: ["post.apellido1:string", "post.id_nom:integer", "post.nuevo:integer", "post.obj_pau:string", "post.sel:mixed", "post.tabla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe la clase de la persona", "No se ha pasado el id_nom", "No se encuentra la persona"]
frontend_referencias: ["frontend/personas/controller/personas_editar.php"]
casos_uso: ["src\\personas\\application\\PersonasEditarData"]
tags: ["personas", "editar", "data"]
estado_revision: "revisado"
---

# Personas Editar Data

Prepara los datos del formulario de ficha de persona (alta o edición). El frontend elige la
plantilla (`persona_form`, `persona_sss_form`, `persona_de_paso`, `p_public_personas`) según
`obj_pau` y permisos de oficina.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- **Alta** (`nuevo=1`): defaults (`situacion=A`, `f_situacion=hoy`, `idioma_preferido`, `dl=mi_dele`),
  precarga `apellido1` (urldecode), asigna `id_nom` nuevo del repositorio del colectivo.
- **Edición**: carga persona por `sel` o `id_nom`; rellena todos los campos de entidad y
  `nom_ctr` si hay `id_ctr`.
- Devuelve mapas de opciones: `opciones_dl`, `opciones_centros` (si no hay ctr fijo),
  `opciones_situacion`, `opciones_lengua`, `opciones_stgr`, `opciones_inc`.
- En alta `PersonaEx`, `opciones_dl` excluye delegaciones ya con esquema propio.

## Endpoint

- URL: `/src/personas/personas_editar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/personas_editar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `nuevo` | `integer` | application | No | `1` → modo alta |
| `obj_pau` | `string` | application | Sí | `PersonaN`, `PersonaAgd`, `PersonaNax`, `PersonaS`, `PersonaSSSC`, `PersonaEx` |
| `sel` | `mixed` | application | No | Edición: `id_nom#id_tabla` |
| `id_nom` | `integer` | application | No | Alternativa a `sel` |
| `apellido1` | `string` | application | No | Solo alta: precarga apellido |
| `tabla` | `string` | application | No | Alta: `id_tabla` inicial |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload con campos del formulario (`trato`, `nom`, apellidos, fechas, `sacd`, `ce*`, `observ`,
  `id_nom`, `id_tabla`, `dl`, `id_ctr`, `nom_ctr`, `titulo`, `edad` en Ex) y los seis mapas
  `opciones_*`.

## Permisos

- Sin control en el caso de uso. El frontend activa edición/guardado con `have_perm_oficina`
  (`sm`, `agd`, `sg`, `des`, `vcsd`, `est`, `dtor` según colectivo).

## Errores conocidos

- `No existe la clase de la persona`
- `No se ha pasado el id_nom` (edición sin id)
- `No se encuentra la persona`

## Casos De Uso

- `src\personas\application\PersonasEditarData`

## Frontend Relacionado

- `frontend/personas/controller/personas_editar.php`
- Vistas: `persona_form.phtml`, `persona_sss_form.phtml`, `persona_de_paso.phtml`, `p_public_personas.phtml`
