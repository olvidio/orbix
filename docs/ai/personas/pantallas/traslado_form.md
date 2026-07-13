---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Traslado de persona"
pantalla: "personas.pantalla.traslado_form"
preguntas: ["Que se puede hacer en Traslado de persona?", "Que campos tiene Traslado de persona?", "Que acciones hay en Traslado de persona?"]
capacidades: ["personas.traslado.gestionar"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
source: "docs/catalogo/personas/pantallas/traslado_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Traslado de persona

## Resumen

Formulario para cambiar centro (`new_ctr`+`f_ctr`) y/o delegación (`new_dl`+`f_dl`+`situacion`). No aplica a personas de paso (`PersonaPub`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.new_ctr`
- `form.f_ctr`
- `form.new_dl`
- `form.f_dl`
- `form.situacion`
- `post.id_pau`
- `post.obj_pau`
- `post.cabecera`

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
