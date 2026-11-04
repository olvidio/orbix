---
id: "misas.quitar_horario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Quitar el horario de una tarea"
capacidad: "misas.quitar_horario.gestionar"
pantallas_principales: ["misas.pantalla.horario_tarea"]
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/quitar_horario"]
estado_revision: "revisado"
---

# Flujo - Quitar horario

## Objetivo De Usuario

Quitar el horario asignado a una tarea de la plantilla.

## Punto De Entrada

Ventana de horario de la tarea.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Quitar el horario de una tarea

Pasos propuestos:
1. Abrir el horario de la tarea.
2. Pulsar **quitar el actual**.

Endpoints asociados:
- `/src/misas/quitar_horario`

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

- `/src/misas/quitar_horario`

## Errores Conocidos

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
