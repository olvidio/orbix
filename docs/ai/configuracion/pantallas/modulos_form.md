---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "configuracion"
titulo: "Ficha de módulo"
pantalla: "configuracion.pantalla.modulos_form"
preguntas: ["Que se puede hacer en Ficha de módulo?", "Que campos tiene Ficha de módulo?", "Que acciones hay en Ficha de módulo?"]
capacidades: ["configuracion.modulos.gestionar"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
source: "docs/catalogo/configuracion/pantallas/modulos_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ficha de módulo

## Resumen

Formulario de alta o edición de un módulo: nombre, descripción, checkboxes de módulos requeridos y aplicaciones requeridas. Las apps heredadas de módulos requeridos aparecen marcadas y deshabilitadas (`a_apps_mod`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.nom`
- `html.descripcion`
- `html.sel_mods[]`
- `html.sel_apps[]`
- `html.id_mod`
- `html.mod`
- `post.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_cambio`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Capacidades Relacionadas

- `configuracion.modulos.gestionar`

## Endpoints Relacionados

- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
