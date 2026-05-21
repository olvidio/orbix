---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Home Persona"
pantalla: "personas.pantalla.home_persona"
preguntas: ["Que se puede hacer en Home Persona?", "Que campos tiene Home Persona?", "Que acciones hay en Home Persona?"]
capacidades: ["personas.home_persona.gestionar"]
endpoints: ["/src/personas/home_persona_data"]
source: "docs/catalogo/personas/pantallas/home_persona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Home Persona

## Resumen

Pantalla de cabecera de una persona (datos basicos + acceso a dossiers y ficha).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_nom`
- `post.id_tabla`
- `post.obj_pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `personas.home_persona.gestionar`

## Endpoints Relacionados

- `/src/personas/home_persona_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
