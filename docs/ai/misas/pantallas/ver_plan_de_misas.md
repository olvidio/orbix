---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Ver Plan De Misas"
pantalla: "misas.pantalla.ver_plan_de_misas"
preguntas: ["Que se puede hacer en Ver Plan De Misas?", "Que campos tiene Ver Plan De Misas?", "Que acciones hay en Ver Plan De Misas?"]
capacidades: ["misas.plan_de_misas_pantalla.gestionar"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
source: "docs/catalogo/misas/pantallas/ver_plan_de_misas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver Plan De Misas

## Resumen

Consulta del plan de misas de una zona en modo solo lectura (cuadrícula sin edición de celdas).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`

## Acciones Detectadas

- `fnjs_ver_cuadricula_zona`

## Capacidades Relacionadas

- `misas.plan_de_misas_pantalla.gestionar`

## Endpoints Relacionados

- `/src/misas/plan_de_misas_pantalla_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
