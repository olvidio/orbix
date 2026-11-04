---
id: "actividades.actividad_fase_completada.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver si una fase está completada"
capacidad: "actividades.actividad_fase_completada.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_fase_completada_datos"]
estado_revision: "revisado"
---

# Flujo - Consultar fase completada

Consulta puntual de si una fase concreta está completada para una actividad (uso AJAX
desde otros módulos o JS de procesos).

## Objetivo De Usuario

Comprobar si una fase del proceso de la actividad está completada, sin recargar la ficha.

## Punto De Entrada

Ficha de actividad (menú **actividades > buscar actividad** y abrir una).

## Escenarios Inferidos

### Ver si una fase está completada

Pasos propuestos:
1. Abrir la ficha de la actividad.
2. Las casillas de fases reflejan si cada fase está completada; se actualizan solas al cambiar algo.

Endpoints asociados:
- `/src/actividades/actividad_fase_completada_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_fase_completada_datos`

## Ruta de menú

sin entrada de menú en el índice.
