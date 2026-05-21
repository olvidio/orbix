---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning Persona Select"
pantalla: "planning.pantalla.planning_persona_select"
preguntas: ["Que se puede hacer en Planning Persona Select?", "Que campos tiene Planning Persona Select?", "Que acciones hay en Planning Persona Select?"]
capacidades: ["planning.planning_persona_select.gestionar"]
endpoints: ["/src/planning/planning_persona_select_data"]
source: "docs/catalogo/planning/pantallas/planning_persona_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning Persona Select

## Resumen

Lista de personas que cumplen los filtros del formulario anterior (`planning_persona_que`).

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
