---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning por centro (calendario)"
pantalla: "planning.pantalla.planning_ctr_select"
preguntas: ["Que se puede hacer en Planning por centro (calendario)?", "Que campos tiene Planning por centro (calendario)?", "Que acciones hay en Planning por centro (calendario)?"]
capacidades: ["planning.planning_ctr_select.gestionar"]
endpoints: ["/src/planning/planning_ctr_select_data"]
source: "docs/catalogo/planning/pantallas/planning_ctr_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning por centro (calendario)

## Resumen

Calendario de personas y actividades agrupadas por centro. Fragmento AJAX cargado desde `planning_ctr_que`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.ctr`
- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.periodo`
- `post.sacd`
- `post.tipo`
- `post.todos_agd`
- `post.todos_n`
- `post.todos_s`
- `post.year`

## Acciones Detectadas

- `fnjs_exportar`

## Capacidades Relacionadas

- `planning.planning_ctr_select.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_ctr_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
