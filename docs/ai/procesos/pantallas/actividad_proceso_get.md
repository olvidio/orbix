---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "procesos"
titulo: "Actividad Proceso Get"
pantalla: "procesos.pantalla.actividad_proceso_get"
preguntas: ["Que se puede hacer en Actividad Proceso Get?", "Que campos tiene Actividad Proceso Get?", "Que acciones hay en Actividad Proceso Get?"]
capacidades: ["procesos.actividad_proceso.gestionar"]
endpoints: ["/src/procesos/actividad_proceso_get"]
source: "docs/catalogo/procesos/pantallas/actividad_proceso_get.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividad Proceso Get

## Resumen

Fragmento AJAX que renderiza la tabla de tareas del proceso de una actividad (fase, tarea, responsable, completado, observaciones y botón guardar).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.b_guardar`
- `html.completado`
- `html.observ`

## Acciones Detectadas

- `fnjs_guardar`

## Capacidades Relacionadas

- `procesos.actividad_proceso.gestionar`

## Endpoints Relacionados

- `/src/procesos/actividad_proceso_get`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
