---
id: "procesos.procesos_get_listado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Get Listado"
capacidad: "procesos.procesos_get_listado.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_get_listado"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_get_listado"]
estado_revision: "revisado"
---

# Flujo - Listado tabular de proceso

## Objetivo De Usuario

Visualización en formato tabla de las fases/tareas de un tipo de proceso, con acciones de modificar y eliminar cada tarea.

## Punto De Entrada

Sin entrada directa de menú; se abre desde la pantalla de administración de procesos (`procesos_select`) con el botón «ver listado».

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_get_listado`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
