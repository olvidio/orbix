---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning Casa Ver"
pantalla: "planning.pantalla.planning_casa_ver"
preguntas: ["Que se puede hacer en Planning Casa Ver?", "Que campos tiene Planning Casa Ver?", "Que acciones hay en Planning Casa Ver?"]
capacidades: ["planning.planning_casa_ver.gestionar"]
endpoints: ["/src/planning/planning_casa_ver_data"]
source: "docs/catalogo/planning/pantallas/planning_casa_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning Casa Ver

## Resumen

Planning (calendario) de actividades de un grupo de casas en un periodo dado.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.periodo`
- `post.propuesta_calendario`
- `post.year`

## Acciones Detectadas

- `fnjs_exportar`

## Capacidades Relacionadas

- `planning.planning_casa_ver.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_casa_ver_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
