---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Publicar persona"
pantalla: "personas.pantalla.persona_publicar_form"
preguntas: ["Que se puede hacer en Publicar persona?", "Que campos tiene Publicar persona?", "Que acciones hay en Publicar persona?"]
capacidades: ["personas.persona_publicar.gestionar"]
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
source: "docs/catalogo/personas/pantallas/persona_publicar_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Publicar persona

## Resumen

Formulario para hacer visible una persona en el desplegable de otra DL durante un mes (caso B / `publicado_para`). Se abre desde el listado de personas con el botón «publicar».

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dl`
- `post.id_nom`
- `post.id_tabla`
- `post.id_schema`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar_publicar`

## Capacidades Relacionadas

- `personas.persona_publicar.gestionar`

## Endpoints Relacionados

- `/src/personas/persona_publicar_form_data`
- `/src/personas/persona_publicar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
