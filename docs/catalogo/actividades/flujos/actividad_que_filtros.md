---
id: "actividades.actividad_que_filtros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Filtros extra buscar actividad"
capacidad: "actividades.actividad_que_filtros.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_que"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_que_filtros"]
estado_revision: "revisado"
---

# Flujo - Filtros extra buscar actividad

Carga AJAX del bloque lugar / organiza / publicada en `actividad_que`.

## Objetivo De Usuario

Tras abrir buscar actividad, ver filtros adicionales según rol (ocultos para usuarios `ctr`).

## Punto De Entrada

`actividad_que.html.twig` → `fnjs_cargar_filtros_extra` al cargar la página.

## Escenarios

### Ejecutar

1. Pantalla buscar actividad lista.
2. JS llama `actividad_que_filtros` con contexto de sesión.
3. HTML devuelto se inserta en `#filtros_extra`.

## Endpoints Del Flujo

- `/src/actividades/actividad_que_filtros`

## Ruta de menú

Herencia de `actividad_que` (buscar activ e importar/publicar).
