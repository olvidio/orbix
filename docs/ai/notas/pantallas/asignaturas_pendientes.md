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

Cuadro "alumnos x asignaturas": genera una tabla con las asignaturas pendientes de todos los alumnos, filtrando por delegacion (`ambito = dl`) o por las delegaciones seleccionadas de la region stgr (`ambito = rstgr`).

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
