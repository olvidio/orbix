---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Stgr Cambio"
pantalla: "personas.pantalla.stgr_cambio"
preguntas: ["Que se puede hacer en Stgr Cambio?", "Que campos tiene Stgr Cambio?", "Que acciones hay en Stgr Cambio?"]
capacidades: ["personas.stgr.gestionar", "personas.stgr_cambio.gestionar"]
endpoints: ["/src/personas/stgr_cambio_data", "/src/personas/stgr_update"]
source: "docs/catalogo/personas/pantallas/stgr_cambio.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Stgr Cambio

## Resumen

Formulario para cambiar el `nivel_stgr` de una persona.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.nivel_stgr`
- `html.guardar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar_stgr`

## Capacidades Relacionadas

- `personas.stgr.gestionar`
- `personas.stgr_cambio.gestionar`

## Endpoints Relacionados

- `/src/personas/stgr_cambio_data`
- `/src/personas/stgr_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
