---
id: "planning.planning_persona_select_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_persona_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/planning/infrastructure/ui/http/controllers/planning_persona_select_data.php"
entrada: ["post.apellido1:string", "post.apellido2:string", "post.centro:string", "post.na:string", "post.nombre:string", "post.obj_pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/planning/controller/planning_persona_select.php"]
casos_uso: ["src\\planning\\application\\PlanningPersonaSelectData"]
tags: ["planning", "persona", "select", "data"]
estado_revision: "revisado"
---

# Planning Persona Select Data

Listado de personas que cumplen los filtros del formulario `planning_persona_que`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca personas activas según criterios opcionales (apellidos, nombre, centro, `na`) en el
repositorio indicado por `obj_pau` (`PersonaDl`, `PersonaSacd`, `PersonaN`, `PersonaEx`, …).
Si hay filtro de centro, resuelve ubis y concatena resultados por ctr.

## Endpoint

- URL: `/src/planning/planning_persona_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_persona_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | No | Colectivo; vacío o `PersonaDl` → DL; `PersonaSacd` → sacd |
| `apellido1` | `string` | application | No | Prefijo sin acentos |
| `apellido2` | `string` | application | No | Prefijo sin acentos |
| `nombre` | `string` | application | No | Prefijo sin acentos (`nom`) |
| `centro` | `string` | application | No | Nombre de ctr; filtra por `id_ctr` |
| `na` | `string` | application | No | Filtro `id_tabla = p{na}` (de paso) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- `data.personas`: lista de `{id_nom, id_tabla, pref_apellidos_nombre, centro_o_dl}`.
- Listado vacío sin error si no hay coincidencias.

## Permisos

- Sin control propio; `obj_pau` y alcance vienen del menú/frontend.

## Casos De Uso

- `src\planning\application\PlanningPersonaSelectData`

## Frontend Relacionado

- `frontend/planning/controller/planning_persona_select.php`
