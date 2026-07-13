---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Ficha de persona"
pantalla: "personas.pantalla.personas_editar"
preguntas: ["Que se puede hacer en Ficha de persona?", "Que campos tiene Ficha de persona?", "Que acciones hay en Ficha de persona?"]
capacidades: ["personas.personas_editar.gestionar", "personas.persona.gestionar"]
endpoints: ["/src/personas/personas_editar_data", "/src/personas/persona_update", "/src/personas/persona_eliminar"]
source: "docs/catalogo/personas/pantallas/personas_editar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ficha de persona

## Resumen

Alta (`nuevo=1`) o edición de persona. Plantilla según colectivo y permiso:

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.nuevo`
- `post.obj_pau`
- `post.sel`
- `post.apellido1`
- `post.tabla`

## Acciones Detectadas

- `fnjs_act_ctr`
- `fnjs_guardar`
- `fnjs_eliminar`

## Capacidades Relacionadas

- `personas.personas_editar.gestionar`
- `personas.persona.gestionar`

## Endpoints Relacionados

- `/src/personas/personas_editar_data`
- `/src/personas/persona_update`
- `/src/personas/persona_eliminar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
