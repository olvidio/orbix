---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Cambiar Status"
pantalla: "misas.pantalla.cambiar_status"
preguntas: ["Que se puede hacer en Cambiar Status?", "Que campos tiene Cambiar Status?", "Que acciones hay en Cambiar Status?"]
capacidades: ["misas.cambiar_status.gestionar", "misas.nuevo_status.gestionar"]
endpoints: ["/src/misas/cambiar_status_data", "/src/misas/nuevo_status"]
source: "docs/catalogo/misas/pantallas/cambiar_status.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Cambiar Status

## Resumen

Pantalla para cambio masivo de estado de encargos en un rango de fechas (`cambiar_status_data`, `nuevo_status`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.estado`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `html.cambiar`

## Acciones Detectadas

- `button:cambiar`
- `fnjs_nuevo_estado`
- `fnjs_ver_cuadricula_zona`

## Capacidades Relacionadas

- `misas.cambiar_status.gestionar`
- `misas.nuevo_status.gestionar`

## Endpoints Relacionados

- `/src/misas/cambiar_status_data`
- `/src/misas/nuevo_status`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
