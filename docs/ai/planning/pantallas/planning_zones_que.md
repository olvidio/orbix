---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning por zonas SACD (filtros)"
pantalla: "planning.pantalla.planning_zones_que"
preguntas: ["Que se puede hacer en Planning por zonas SACD (filtros)?", "Que campos tiene Planning por zonas SACD (filtros)?", "Que acciones hay en Planning por zonas SACD (filtros)?"]
capacidades: ["planning.planning_zones_que.gestionar"]
endpoints: ["/src/planning/planning_zones_que_data"]
source: "docs/catalogo/planning/pantallas/planning_zones_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning por zonas SACD (filtros)

## Resumen

Formulario: zona SACD, trimestre, año y filtro de actividad. Carga zonas permitidas vía `planning_zones_que_data`. Al enviar abre `planning_zones_select`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.actividad`
- `form.id_zona`
- `form.trimestre`
- `form.year`
- `html.actividad`
- `html.id_zona`
- `html.trimestre`
- `post.actividad`
- `post.id_zona`
- `post.modo`
- `post.stack`
- `post.trimestre`
- `post.year`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_ver_planning`

## Capacidades Relacionadas

- `planning.planning_zones_que.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_zones_que_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
