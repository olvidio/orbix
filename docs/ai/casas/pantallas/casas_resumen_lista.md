---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Casas Resumen Lista"
pantalla: "casas.pantalla.casas_resumen_lista"
preguntas: ["Que se puede hacer en Casas Resumen Lista?", "Que campos tiene Casas Resumen Lista?", "Que acciones hay en Casas Resumen Lista?"]
capacidades: ["casas.casas_resumen.gestionar"]
endpoints: ["/src/casas/casas_resumen_data"]
source: "docs/catalogo/casas/pantallas/casas_resumen_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Casas Resumen Lista

## Resumen

Controlador AJAX HTML: resumen económico de casas (modo periodo y modo anual 5 años).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `casas.casas_resumen.gestionar`

## Endpoints Relacionados

- `/src/casas/casas_resumen_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
