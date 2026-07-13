---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "planning"
titulo: "Planning por zonas SACD (calendario)"
pantalla: "planning.pantalla.planning_zones_select"
preguntas: ["Que se puede hacer en Planning por zonas SACD (calendario)?", "Que campos tiene Planning por zonas SACD (calendario)?", "Que acciones hay en Planning por zonas SACD (calendario)?"]
capacidades: ["planning.planning_zones_select.gestionar"]
endpoints: ["/src/planning/planning_zones_select_data"]
source: "docs/catalogo/planning/pantallas/planning_zones_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Planning por zonas SACD (calendario)

## Resumen

Cuadrícula de actividades por zona SACD en el trimestre elegido. Fragmento AJAX desde `planning_zones_que`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.actividad`
- `post.id_zona`
- `post.modelo`
- `post.propuesta`
- `post.trimestre`
- `post.year`

## Acciones Detectadas

- `fnjs_exportar`

## Capacidades Relacionadas

- `planning.planning_zones_select.gestionar`

## Endpoints Relacionados

- `/src/planning/planning_zones_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
