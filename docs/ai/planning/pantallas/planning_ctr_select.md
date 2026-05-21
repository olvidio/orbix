---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning Ctr Select"
pantalla: "planning.pantalla.planning_ctr_select"
preguntas: ["Que se puede hacer en Planning Ctr Select?", "Que campos tiene Planning Ctr Select?", "Que acciones hay en Planning Ctr Select?"]
capacidades: ["planning.planning_ctr_select.gestionar"]
endpoints: ["/src/planning/planning_ctr_select_data"]
source: "docs/catalogo/planning/pantallas/planning_ctr_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning Ctr Select

## Resumen

Planning (calendario) de las personas de un centro (o grupo de centros), filtrado por periodo y tipo de persona (n, agd, s).

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
