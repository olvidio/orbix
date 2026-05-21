---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Grupo Form"
pantalla: "casas.pantalla.grupo_form"
preguntas: ["Que se puede hacer en Grupo Form?", "Que campos tiene Grupo Form?", "Que acciones hay en Grupo Form?"]
capacidades: ["casas.grupo.gestionar"]
endpoints: ["/src/casas/grupo_form_data"]
source: "docs/catalogo/casas/pantallas/grupo_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Grupo Form

## Resumen

Controlador AJAX HTML: formulario `GrupoCasa` (nuevo/editar).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.cancelar`
- `html.id_item`
- `html.ok`
- `post.id_item`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `casas.grupo.gestionar`

## Endpoints Relacionados

- `/src/casas/grupo_form_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
