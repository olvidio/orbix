---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Actividades por periodo"
pantalla: "inventario.pantalla.equipajes_lista_activ_periodo"
preguntas: ["Que se puede hacer en Actividades por periodo?", "Que campos tiene Actividades por periodo?", "Que acciones hay en Actividades por periodo?"]
capacidades: ["inventario.equipajes_lista_activ_periodo.gestionar"]
endpoints: ["/src/inventario/equipajes_lista_activ_periodo"]
source: "docs/catalogo/inventario/pantallas/equipajes_lista_activ_periodo.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividades por periodo

## Resumen

Tabla actividades filtradas por CDC y periodo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc`
- `post.inicio`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_nombrar_equipaje`

## Capacidades Relacionadas

- `inventario.equipajes_lista_activ_periodo.gestionar`

## Endpoints Relacionados

- `/src/inventario/equipajes_lista_activ_periodo`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
