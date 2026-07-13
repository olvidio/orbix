---
id: "procesos.actividad_proceso_generar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Actividad Proceso Generar"
capacidad: "procesos.actividad_proceso_generar.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.actividad_proceso"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/actividad_proceso_generar"]
estado_revision: "revisado"
---

# Flujo - Regenerar proceso de actividad

## Objetivo De Usuario

Regenerar las tareas del proceso asociado a una actividad, conservando o no el estado actual según el flag «forzar».

## Punto De Entrada

Sin entrada directa de menú; se ejecuta desde la pantalla embebida de proceso de actividad.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.actividad_proceso`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
