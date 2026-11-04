---
id: "misas.guardar_horario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Guardar el horario de una tarea"
capacidad: "misas.guardar_horario.gestionar"
pantallas_principales: ["misas.pantalla.horario_tarea"]
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_horario"]
estado_revision: "revisado"
---

# Flujo - Guardar horario

## Objetivo De Usuario

Guardar la hora de inicio y fin de una tarea.

## Punto De Entrada

Ventana de horario de la tarea.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Guardar el horario de una tarea

Pasos propuestos:
1. Escribir **Inicio** y **Fin**.
2. Pulsar **guardar**.

Endpoints asociados:
- `/src/misas/guardar_horario`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_item`
- `form.t_end`
- `form.t_start`
- `post.id_item_h`

Acciones JavaScript:
- `fnjs_guardar_horario`
- `fnjs_quitar_horario`

## Endpoints Del Flujo

- `/src/misas/guardar_horario`

## Errores Conocidos

- `Error: falta el id_item`
- `No se encuentra el horario %d`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
