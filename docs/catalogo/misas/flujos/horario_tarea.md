---
id: "misas.horario_tarea.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Poner horario a una tarea"
capacidad: "misas.horario_tarea.gestionar"
pantallas_principales: ["misas.pantalla.horario_tarea"]
fragmentos: ["misas.pantalla.horario_tarea"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/horario_tarea_data"]
estado_revision: "revisado"
---

# Flujo - Horario tarea

## Objetivo De Usuario

Ver y fijar la hora de inicio y fin de una tarea (encargo) en la plantilla.

## Punto De Entrada

Ventana de horario de una tarea en **Modificar plantilla**.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.horario_tarea`

## Escenarios Inferidos

### Poner horario a una tarea

Pasos propuestos:
1. En **Modificar plantilla**, abrir el horario de la tarea.
2. Escribir **Inicio** y **Fin** y pulsar **guardar**.

Endpoints asociados:
- `/src/misas/horario_tarea_data`

### Quitar el horario de una tarea

Pasos propuestos:
1. Abrir el horario de la tarea y pulsar **quitar el actual**.

Endpoints asociados:
- `/src/misas/horario_tarea_data`

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

- `/src/misas/horario_tarea_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
