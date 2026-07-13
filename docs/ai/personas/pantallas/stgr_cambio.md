---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Cambio nivel STGR"
pantalla: "personas.pantalla.stgr_cambio"
preguntas: ["Que se puede hacer en Cambio nivel STGR?", "Que campos tiene Cambio nivel STGR?", "Que acciones hay en Cambio nivel STGR?"]
capacidades: ["personas.stgr.gestionar", "personas.stgr_cambio.gestionar"]
endpoints: ["/src/personas/stgr_cambio_data", "/src/personas/stgr_update"]
source: "docs/catalogo/personas/pantallas/stgr_cambio.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Cambio nivel STGR

## Resumen

Formulario modal con desplegable de niveles STGR para una persona seleccionada en el listado.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.nivel_stgr`
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
