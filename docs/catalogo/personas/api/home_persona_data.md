---
id: "personas.home_persona_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/home_persona_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/personas/infrastructure/ui/http/controllers/home_persona_data.php"
entrada: ["post.id_nom:integer", "post.id_tabla:string", "post.obj_pau:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe la clase de la persona", "No se encuentra la persona"]
frontend_referencias: ["frontend/personas/controller/home_persona.php"]
casos_uso: ["src\\personas\\application\\HomePersonaData"]
tags: ["personas", "home", "persona", "data"]
estado_revision: "revisado"
---

# Home Persona Data

Construye el payload de la cabecera de persona (`home_persona.phtml`): datos básicos, telecos,
centro, nivel STGR traducido y normalización de `Qobj_pau` cuando la entidad es `PersonaDl`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga una persona por `sel` (`id_nom#id_tabla`) o por `id_nom`+`id_tabla`/`obj_pau`. Resuelve
repositorio según `obj_pau` (`PersonaEx` usa `PersonaPubRepository::findByIdParaListado`).
Si `id_nom<=0` y no hay persona → aviso suave «persona no válida» (no error duro). Si
`PersonaDl` genérico, remapea `Qobj_pau` a subclase (`PersonaN`, `PersonaAgd`, `PersonaS`,
`PersonaSSSC`) según `id_tabla`. Añade teléfonos fijo/móvil y e-mails vía `TelecoPersonaService`.

## Endpoint

- URL: `/src/personas/home_persona_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/home_persona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | application | No | Token `id_nom#id_tabla` (array o string) |
| `id_nom` | `integer` | application | No | Alternativa a `sel` |
| `id_tabla` | `string` | application | No | Con `id_nom` si no hay `sel` |
| `obj_pau` | `string` | application | No | `PersonaN`, `PersonaAgd`, `PersonaEx`, etc. |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Claves: `Qobj_pau`, `id_nom`, `id_tabla`, `titulo`, `dl`, `f_nacimiento`, `situacion`,
  `f_situacion`, `profesion`, `stgr` (etiqueta nivel), `observ`, `ctr` (nombre centro),
  `telfs`, `mails`, opcional `aviso` (región STGR o persona no válida).

## Permisos

- Sin control en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Errores conocidos

- `No existe la clase de la persona` (`InvalidArgumentException` en resolver)
- `No se encuentra la persona` (`id_nom>0` sin registro)
- Aviso suave: persona no válida (`id_nom<=0`)

## Casos De Uso

- `src\personas\application\HomePersonaData`

## Frontend Relacionado

- `frontend/personas/controller/home_persona.php` (desde listado `fnjs_home` o enlace directo)
