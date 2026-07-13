---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Ver Plan Sacd"
pantalla: "misas.pantalla.ver_plan_sacd"
preguntas: ["Que se puede hacer en Ver Plan Sacd?", "Que campos tiene Ver Plan Sacd?", "Que acciones hay en Ver Plan Sacd?"]
capacidades: ["misas.ver_plan_sacd.gestionar"]
endpoints: ["/src/misas/ver_plan_sacd_data"]
source: "docs/catalogo/misas/pantallas/ver_plan_sacd.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver Plan Sacd

## Resumen

Resultado: lista cronológica de misas del sacerdote (`ver_plan_sacd_data`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_sacd`
- `post.periodo`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `misas.ver_plan_sacd.gestionar`

## Endpoints Relacionados

- `/src/misas/ver_plan_sacd_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
