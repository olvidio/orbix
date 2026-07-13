---
id: "actividades.actividad_fases_completadas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Prefill fases completadas"
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

Ver checkboxes de fases coherentes con el estado real del proceso al editar/crear.

## Punto De Entrada

Carga de `actividad_ver` (servidor) antes de renderizar formulario.

## Endpoints Del Flujo

- `/src/actividades/actividad_fases_completadas_datos`

## Ruta de menú

sin entrada propia (requiere módulo procesos; acceso vía ficha actividad).
