---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Imprimir Plan Ctr"
pantalla: "misas.pantalla.imprimir_plan_ctr"
preguntas: ["Que se puede hacer en Imprimir Plan Ctr?", "Que campos tiene Imprimir Plan Ctr?", "Que acciones hay en Imprimir Plan Ctr?"]
capacidades: ["misas.ver_plan_ctr.gestionar"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
source: "docs/catalogo/misas/pantallas/imprimir_plan_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Imprimir Plan Ctr

## Resumen

Generación PDF/mpdf del plan CTR a partir de `ver_plan_ctr`. Sin menú directo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.id_zona`
- `post.periodo`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `misas.ver_plan_ctr.gestionar`

## Endpoints Relacionados

- `/src/misas/ver_plan_ctr_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
