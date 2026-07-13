---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Cabecera de persona"
pantalla: "personas.pantalla.home_persona"
preguntas: ["Que se puede hacer en Cabecera de persona?", "Que campos tiene Cabecera de persona?", "Que acciones hay en Cabecera de persona?"]
capacidades: ["personas.home_persona.gestionar"]
endpoints: ["/src/personas/home_persona_data"]
source: "docs/catalogo/personas/pantallas/home_persona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Cabecera de persona

## Resumen

Resumen de datos básicos (nombre, dl, centro, STGR, teléfonos, e-mail, situación) y accesos a ficha, dossiers y lista embebida de dossiers.

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
