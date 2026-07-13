---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "procesos"
titulo: "Actividad Proceso"
pantalla: "procesos.pantalla.actividad_proceso"
preguntas: ["Que se puede hacer en Actividad Proceso?", "Que campos tiene Actividad Proceso?", "Que acciones hay en Actividad Proceso?"]
capacidades: ["procesos.actividad_proceso.gestionar", "procesos.actividad_proceso_generar.gestionar"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_generar", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
source: "docs/catalogo/procesos/pantallas/actividad_proceso.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividad Proceso

## Resumen

Vista del proceso de una actividad concreta: tabla de fases/tareas con completado y observaciones, opción forzar, regenerar proceso (con permiso calendario) y enlace al dossier de la actividad.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.completado`
- `form.force`
- `form.id_item`
- `form.observ`
- `post.id_activ`
- `post.sel`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `procesos.actividad_proceso.gestionar`
- `procesos.actividad_proceso_generar.gestionar`

## Endpoints Relacionados

- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_generar`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
