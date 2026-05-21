---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Traslado Form"
pantalla: "personas.pantalla.traslado_form"
preguntas: ["Que se puede hacer en Traslado Form?", "Que campos tiene Traslado Form?", "Que acciones hay en Traslado Form?"]
capacidades: ["personas.traslado.gestionar"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
source: "docs/catalogo/personas/pantallas/traslado_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Traslado Form

## Resumen

Formulario para trasladar una persona de centro y/o delegacion.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.f_ctr`
- `form.f_dl`
- `form.new_ctr`
- `form.new_dl`
- `form.situacion`
- `html.f_ctr`
- `html.f_dl`
- `post.cabecera`
- `post.id_pau`
- `post.obj_pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `personas.traslado.gestionar`

## Endpoints Relacionados

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
