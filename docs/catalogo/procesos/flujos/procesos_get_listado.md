---
id: "procesos.procesos_get_listado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Consultar y modificar las tareas de un proceso"
capacidad: "procesos.procesos_get_listado.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_get_listado"]
fragmentos: ["procesos.pantalla.procesos_get_listado"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_get_listado"]
estado_revision: "revisado"
---

# Flujo - Listado tabular de proceso

## Objetivo De Usuario

Ver las fases y tareas de un tipo de proceso en forma de tabla, con acciones para modificar o eliminar cada tarea.

## Punto De Entrada

Menú **sistema > procesos activ. > procesos** (Pills2: ADMIN LOCAL > procesos activ. > procesos).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_get_listado`

## Escenarios Inferidos

### Consultar y modificar las tareas de un proceso

Pasos propuestos:
1. En **procesos**, elegir el proceso y pulsar **ver listado**.
2. Se muestra una tabla con cada fase y tarea.
3. Pulsar una tarea para modificarla, o su botón de eliminar para quitarla (con confirmación).
4. Para añadir una tarea, elegir el proceso y pulsar **nuevo**.

Endpoints asociados:
- `/src/procesos/procesos_get_listado`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_eliminar`
- `fnjs_modificar`

## Endpoints Del Flujo

- `/src/procesos/procesos_get_listado`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
