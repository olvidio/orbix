---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Horario Tarea"
pantalla: "misas.pantalla.horario_tarea"
preguntas: ["Que se puede hacer en Horario Tarea?", "Que campos tiene Horario Tarea?", "Que acciones hay en Horario Tarea?"]
capacidades: ["misas.guardar_horario.gestionar", "misas.horario_tarea.gestionar", "misas.quitar_horario.gestionar"]
endpoints: ["/src/misas/guardar_horario", "/src/misas/horario_tarea_data", "/src/misas/quitar_horario"]
source: "docs/catalogo/misas/pantallas/horario_tarea.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Horario Tarea

## Resumen

Descripcion funcional pendiente de revisar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_item`
- `form.t_end`
- `form.t_start`
- `post.id_item_h`

## Acciones Detectadas

- `fnjs_guardar_horario`
- `fnjs_quitar_horario`

## Capacidades Relacionadas

- `misas.guardar_horario.gestionar`
- `misas.horario_tarea.gestionar`
- `misas.quitar_horario.gestionar`

## Endpoints Relacionados

- `/src/misas/guardar_horario`
- `/src/misas/horario_tarea_data`
- `/src/misas/quitar_horario`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
