---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "procesos"
titulo: "Procesos Select"
pantalla: "procesos.pantalla.procesos_select"
preguntas: ["Que se puede hacer en Procesos Select?", "Que campos tiene Procesos Select?", "Que acciones hay en Procesos Select?"]
capacidades: ["procesos.procesos.gestionar", "procesos.procesos_clonar.gestionar", "procesos.procesos_regenerar.gestionar", "procesos.procesos_select.gestionar"]
endpoints: ["/src/procesos/procesos_clonar", "/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_regenerar", "/src/procesos/procesos_select_data", "/src/procesos/procesos_update"]
source: "docs/catalogo/procesos/pantallas/procesos_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Procesos Select

## Resumen

Administración de tipos de proceso: desplegable de proceso, visualización en árbol o listado tabular de fases/tareas, alta y edición en ventana modal, clonado desde otro proceso y regeneración masiva de procesos en actividades.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.refresh`
- `post.stack`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `procesos.procesos.gestionar`
- `procesos.procesos_clonar.gestionar`
- `procesos.procesos_regenerar.gestionar`
- `procesos.procesos_select.gestionar`

## Endpoints Relacionados

- `/src/procesos/procesos_clonar`
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_regenerar`
- `/src/procesos/procesos_select_data`
- `/src/procesos/procesos_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
