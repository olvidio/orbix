---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "casas"
titulo: "Casa Actividades Lista"
pantalla: "casas.pantalla.casa_actividades_lista"
preguntas: ["Que se puede hacer en Casa Actividades Lista?", "Que campos tiene Casa Actividades Lista?", "Que acciones hay en Casa Actividades Lista?"]
capacidades: ["casas.casa_actividades.gestionar"]
endpoints: ["/src/casas/casa_actividades_lista_data"]
source: "docs/catalogo/casas/pantallas/casa_actividades_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Casa Actividades Lista

## Resumen

Controlador AJAX HTML: listado de actividades por casa y periodo (`tipo_lista=lista_activ`).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `casas.casa_actividades.gestionar`

## Endpoints Relacionados

- `/src/casas/casa_actividades_lista_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
