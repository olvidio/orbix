---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Calendario Listas"
pantalla: "actividades.pantalla.calendario_listas"
preguntas: ["Que se puede hacer en Calendario Listas?", "Que campos tiene Calendario Listas?", "Que acciones hay en Calendario Listas?"]
capacidades: ["actividades.calendario_listas.gestionar"]
endpoints: ["/src/actividades/calendario_listas_datos"]
source: "docs/catalogo/actividades/pantallas/calendario_listas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Calendario Listas

## Resumen

Fragmento HTML con el calendario de actividades de casas / oficinas en un periodo dado.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.ver_ctr`
- `post.year`
- `post.yeardefault`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.calendario_listas.gestionar`

## Endpoints Relacionados

- `/src/actividades/calendario_listas_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
