---
id: "actividades.actividad_fases_completadas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver las fases completadas de una actividad"
capacidad: "actividades.actividad_fases_completadas.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_fases_completadas_datos"]
estado_revision: "revisado"
---

# Flujo - Prefill fases completadas

Con `procesos` instalado, marca fases ya completadas al abrir ficha (helper
`PrefillPermActividadesFases`).

## Objetivo De Usuario

Mostrar en la ficha las fases completadas coherentes con el estado real del proceso.

## Punto De Entrada

Ficha de actividad (menú **actividades > buscar actividad** y abrir una).

## Escenarios Inferidos

### Ver las fases completadas de una actividad

Pasos propuestos:
1. Abrir la ficha de una actividad existente o crear una nueva.
2. Las casillas de fase aparecen marcadas según el estado real del proceso.

Endpoints asociados:
- `/src/actividades/actividad_fases_completadas_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_fases_completadas_datos`

## Ruta de menú

sin entrada propia (requiere módulo procesos; acceso vía ficha actividad).
