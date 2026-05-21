---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Prevision Asistentes"
pantalla: "casas.pantalla.prevision_asistentes"
preguntas: ["Que se puede hacer en Prevision Asistentes?", "Que campos tiene Prevision Asistentes?", "Que acciones hay en Prevision Asistentes?"]
capacidades: ["casas.ingreso_plazas_previstas.gestionar", "casas.prevision_asistentes.gestionar"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update", "/src/casas/prevision_asistentes_data"]
source: "docs/catalogo/casas/pantallas/prevision_asistentes.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Prevision Asistentes

## Resumen

Pantalla `prevision_asistentes`: tabla editable con las plazas previstas por actividad.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.mi_of`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mi_of`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `casas.ingreso_plazas_previstas.gestionar`
- `casas.prevision_asistentes.gestionar`

## Endpoints Relacionados

- `/src/casas/ingreso_plazas_previstas_update`
- `/src/casas/prevision_asistentes_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
