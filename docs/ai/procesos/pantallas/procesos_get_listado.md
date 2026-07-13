---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "procesos"
titulo: "Procesos Get Listado"
pantalla: "procesos.pantalla.procesos_get_listado"
preguntas: ["Que se puede hacer en Procesos Get Listado?", "Que campos tiene Procesos Get Listado?", "Que acciones hay en Procesos Get Listado?"]
capacidades: ["procesos.procesos_get_listado.gestionar"]
endpoints: ["/src/procesos/procesos_get_listado"]
source: "docs/catalogo/procesos/pantallas/procesos_get_listado.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Procesos Get Listado

## Resumen

Fragmento AJAX que renderiza la tabla tabular de fases/tareas del proceso con acciones para modificar y eliminar cada tarea.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- No hay campos detectados.

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_modificar`

## Capacidades Relacionadas

- `procesos.procesos_get_listado.gestionar`

## Endpoints Relacionados

- `/src/procesos/procesos_get_listado`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
