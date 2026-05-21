---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadplazas"
titulo: "Resumen Plazas"
pantalla: "actividadplazas.pantalla.resumen_plazas"
preguntas: ["Que se puede hacer en Resumen Plazas?", "Que campos tiene Resumen Plazas?", "Que acciones hay en Resumen Plazas?"]
capacidades: ["actividadplazas.plazas_ceder.gestionar", "actividadplazas.resumen_plazas.gestionar"]
endpoints: ["/src/actividadplazas/plazas_ceder", "/src/actividadplazas/resumen_plazas_data"]
source: "docs/catalogo/actividadplazas/pantallas/resumen_plazas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Resumen Plazas

## Resumen

Pantalla resumen de plazas por actividad.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_activ`
- `form.num_plazas`
- `form.region_dl`
- `html.btn_ok`
- `html.num_plazas`
- `html.refresh`
- `post.id_activ`
- `post.nom_activ`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Capacidades Relacionadas

- `actividadplazas.plazas_ceder.gestionar`
- `actividadplazas.resumen_plazas.gestionar`

## Endpoints Relacionados

- `/src/actividadplazas/plazas_ceder`
- `/src/actividadplazas/resumen_plazas_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
