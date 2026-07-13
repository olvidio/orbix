---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Modificar Plan De Misas"
pantalla: "misas.pantalla.modificar_plan_de_misas"
preguntas: ["Que se puede hacer en Modificar Plan De Misas?", "Que campos tiene Modificar Plan De Misas?", "Que acciones hay en Modificar Plan De Misas?"]
capacidades: ["misas.plan_de_misas_pantalla.gestionar"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
source: "docs/catalogo/misas/pantallas/modificar_plan_de_misas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Modificar Plan De Misas

## Resumen

Formulario para editar plan existente: zona, orden y rango de fechas. Carga cuadrícula `ver_cuadricula_zona`.

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

- `fnjs_modificar_cuadricula_zona`

## Capacidades Relacionadas

- `misas.plan_de_misas_pantalla.gestionar`

## Endpoints Relacionados

- `/src/misas/plan_de_misas_pantalla_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
