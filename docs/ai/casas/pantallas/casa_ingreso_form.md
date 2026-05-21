---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Casa Ingreso Form"
pantalla: "casas.pantalla.casa_ingreso_form"
preguntas: ["Que se puede hacer en Casa Ingreso Form?", "Que campos tiene Casa Ingreso Form?", "Que acciones hay en Casa Ingreso Form?"]
capacidades: ["casas.casa_ingreso.gestionar"]
endpoints: ["/src/casas/casa_ingreso_form_data"]
source: "docs/catalogo/casas/pantallas/casa_ingreso_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Casa Ingreso Form

## Resumen

Controlador AJAX HTML: formulario modal del ingreso de una actividad (edición).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `get.id_activ`
- `html.id_activ`
- `html.ingresos`
- `html.num_asistentes`
- `html.observ`
- `html.precio`
- `post.id_activ`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Capacidades Relacionadas

- `casas.casa_ingreso.gestionar`

## Endpoints Relacionados

- `/src/casas/casa_ingreso_form_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
