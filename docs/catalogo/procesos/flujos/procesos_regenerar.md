---
id: "procesos.procesos_regenerar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Regenerar las actividades de un proceso"
capacidad: "procesos.procesos_regenerar.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_select"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_regenerar"]
estado_revision: "revisado"
---

# Flujo - Regenerar procesos en actividades

## Objetivo De Usuario

Regenerar las tareas de todas las actividades que usan un tipo de proceso, tras cambiar su definición.

## Punto De Entrada

Menú **sistema > procesos activ. > procesos** (Pills2: ADMIN LOCAL > procesos activ. > procesos).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_select`

## Escenarios Inferidos

### Regenerar las actividades de un proceso

Pasos propuestos:
1. En **procesos**, elegir el proceso.
2. Pulsar **rehacer los procesos de las actividades**. Las tareas de las actividades asociadas se recalculan a partir de la definición actual de fases y tareas.

Endpoints asociados:
- `/src/procesos/procesos_regenerar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.refresh`
- `post.stack`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/procesos_regenerar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
