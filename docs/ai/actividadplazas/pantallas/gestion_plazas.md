---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadplazas"
titulo: "Gestion Plazas"
pantalla: "actividadplazas.pantalla.gestion_plazas"
preguntas: ["Que se puede hacer en Gestion Plazas?", "Que campos tiene Gestion Plazas?", "Que acciones hay en Gestion Plazas?"]
capacidades: ["actividadplazas.gestion_plazas.gestionar"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
source: "docs/catalogo/actividadplazas/pantallas/gestion_plazas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Gestion Plazas

## Resumen

Pantalla principal del módulo `actividadplazas`: cuadro de distribución de plazas por delegación del grupo de estudios (totales, concedidas y pedidas) para un periodo y tipo de actividad, con edición inline por celda.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.colName`
- `form.data`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_tipo_activ`
- `post.periodo`
- `post.refresh`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `actividadplazas.gestion_plazas.gestionar`

## Endpoints Relacionados

- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
