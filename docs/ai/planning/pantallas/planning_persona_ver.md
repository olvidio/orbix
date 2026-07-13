---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning por persona (calendario)"
pantalla: "planning.pantalla.planning_persona_ver"
preguntas: ["Que se puede hacer en Planning por persona (calendario)?", "Que campos tiene Planning por persona (calendario)?", "Que acciones hay en Planning por persona (calendario)?"]
capacidades: ["planning.planning_persona_ver.gestionar"]
endpoints: ["/src/planning/planning_persona_ver_data"]
source: "docs/catalogo/planning/pantallas/planning_persona_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning por persona (calendario)

## Resumen

Calendario de actividades de las personas seleccionadas en `planning_persona_select`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.obj_pau`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_exportar`

## Capacidades Relacionadas

- `planning.planning_persona_ver.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_persona_ver_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
