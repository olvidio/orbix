---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadplazas"
titulo: "Peticiones Activ"
pantalla: "actividadplazas.pantalla.peticiones_activ"
preguntas: ["Que se puede hacer en Peticiones Activ?", "Que campos tiene Peticiones Activ?", "Que acciones hay en Peticiones Activ?"]
capacidades: ["actividadplazas.peticiones.gestionar", "actividadplazas.peticiones_activ.gestionar"]
endpoints: ["/src/actividadplazas/peticiones_activ_data", "/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
source: "docs/catalogo/actividadplazas/pantallas/peticiones_activ.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Peticiones Activ

## Resumen

Pantalla de peticiones de plaza de una persona (n / a / agd).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.actividades`
- `form.actividades_mas`
- `form.actividades_num`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.id_nom`
- `post.na`
- `post.que`
- `post.sactividad`
- `post.sel`
- `post.stack`
- `post.todos`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_slide_atras`
- `fnjs_mas_actividades`

## Capacidades Relacionadas

- `actividadplazas.peticiones.gestionar`
- `actividadplazas.peticiones_activ.gestionar`

## Endpoints Relacionados

- `/src/actividadplazas/peticiones_activ_data`
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
