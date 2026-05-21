---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Personas Editar"
pantalla: "personas.pantalla.personas_editar"
preguntas: ["Que se puede hacer en Personas Editar?", "Que campos tiene Personas Editar?", "Que acciones hay en Personas Editar?"]
capacidades: ["personas.personas_editar.gestionar"]
endpoints: ["/src/personas/personas_editar_data"]
source: "docs/catalogo/personas/pantallas/personas_editar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Personas Editar

## Resumen

Ficha de una persona: edicion (o alta si `$Qnuevo === 1`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.apellido1`
- `post.id_nom`
- `post.nuevo`
- `post.obj_pau`
- `post.sel`
- `post.stack`
- `post.tabla`

## Acciones Detectadas

- `fnjs_act_ctr`

## Capacidades Relacionadas

- `personas.personas_editar.gestionar`

## Endpoints Relacionados

- `/src/personas/personas_editar_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
