---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Listado de personas (planning)"
pantalla: "planning.pantalla.planning_persona_select"
preguntas: ["Que se puede hacer en Listado de personas (planning)?", "Que campos tiene Listado de personas (planning)?", "Que acciones hay en Listado de personas (planning)?"]
capacidades: ["planning.planning_persona_select.gestionar"]
endpoints: ["/src/planning/planning_persona_select_data"]
source: "docs/catalogo/planning/pantallas/planning_persona_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listado de personas (planning)

## Resumen

Tabla de personas que cumplen los filtros. Llama a `planning_persona_select_data` y permite ver el planning de la selección o acceder a ficha/dossier.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.id_dossier`
- `html.modelo`
- `html.que`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_sel`
- `post.na`
- `post.nombre`
- `post.obj_pau`
- `post.periodo`
- `post.scroll_id`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_actividades`
- `fnjs_enviar_formulario`
- `fnjs_planning_print`
- `fnjs_solo_uno`
- `fnjs_ver_planning`

## Capacidades Relacionadas

- `planning.planning_persona_select.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_persona_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
