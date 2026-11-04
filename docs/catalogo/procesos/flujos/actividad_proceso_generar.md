---
id: "procesos.actividad_proceso_generar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Regenerar el proceso de una actividad"
capacidad: "procesos.actividad_proceso_generar.gestionar"
pantallas_principales: ["procesos.pantalla.actividad_proceso"]
fragmentos: ["procesos.pantalla.actividad_proceso"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/actividad_proceso_generar"]
estado_revision: "revisado"
---

# Flujo - Regenerar proceso de actividad

## Objetivo De Usuario

Regenerar las tareas del proceso de una actividad, conservando o no su estado actual.

## Punto De Entrada

Pestaña de proceso de la ficha de la actividad.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.actividad_proceso`

## Escenarios Inferidos

### Regenerar el proceso de una actividad

Pasos propuestos:
1. Abrir la ficha de la actividad y su pestaña de proceso.
2. Pulsar regenerar.
3. Marcar «forzar» si se quiere descartar el estado actual de las tareas; si no, se conserva.

Endpoints asociados:
- `/src/procesos/actividad_proceso_generar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.completado`
- `form.force`
- `form.id_item`
- `form.observ`
- `post.id_activ`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/actividad_proceso_generar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
