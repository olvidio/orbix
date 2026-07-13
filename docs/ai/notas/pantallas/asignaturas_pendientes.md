---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Asignaturas Pendientes"
pantalla: "notas.pantalla.asignaturas_pendientes"
preguntas: ["Que se puede hacer en Asignaturas Pendientes?", "Que campos tiene Asignaturas Pendientes?", "Que acciones hay en Asignaturas Pendientes?"]
capacidades: ["notas.asignaturas_pendientes.gestionar"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
source: "docs/catalogo/notas/pantallas/asignaturas_pendientes.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Asignaturas Pendientes

## Resumen

Matriz alumnos × asignaturas pendientes de superar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl`
- `post.dl`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `notas.asignaturas_pendientes.gestionar`

## Endpoints Relacionados

- `/src/notas/asignaturas_pendientes_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
