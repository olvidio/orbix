---
id: "actividades.actividad_fase_completada.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Consultar fase completada"
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

Validar estado de una fase sin recargar toda la ficha.

## Punto De Entrada

Llamadas AJAX desde integraciones con `procesos` (no pantalla dedicada en actividades).

## Endpoints Del Flujo

- `/src/actividades/actividad_fase_completada_datos`

## Ruta de menú

sin entrada de menú en el índice.
