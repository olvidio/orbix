---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Acta Listado Anual"
pantalla: "notas.pantalla.acta_listado_anual"
preguntas: ["Que se puede hacer en Acta Listado Anual?", "Que campos tiene Acta Listado Anual?", "Que acciones hay en Acta Listado Anual?"]
capacidades: ["notas.acta_listado_anual.gestionar"]
endpoints: ["/src/notas/acta_listado_anual_data"]
source: "docs/catalogo/notas/pantallas/acta_listado_anual.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Acta Listado Anual

## Resumen

Listado anual de actas filtrable por rango de fechas.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Capacidades Relacionadas

- `notas.acta_listado_anual.gestionar`

## Endpoints Relacionados

- `/src/notas/acta_listado_anual_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
