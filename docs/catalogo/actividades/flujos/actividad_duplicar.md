---
id: "actividades.actividad_duplicar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Duplicar actividad"
capacidad: "actividades.actividad_duplicar.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_select"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_duplicar"]
estado_revision: "revisado"
---

# Flujo - Duplicar actividad

Crear una copia de actividad(es) de la propia dl desde un listado.

## Objetivo De Usuario

Seleccionar actividad origen y duplicarla (nueva ficha en proyecto).

## Punto De Entrada

Acción en `actividad_select` u otros listados que expongan duplicar (según permisos).

## Escenarios

### Ejecutar

1. Localizar actividad en listado de búsqueda.
2. Elegir duplicar (una selección).
3. POST `actividad_duplicar`; abrir o localizar la copia.

## Endpoints Del Flujo

- `/src/actividades/actividad_duplicar`

## Errores Conocidos

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`
- `hay un error, no se ha guardado` + detalle

## Ruta de menú

sin entrada de menú en el índice (acción desde listado de búsqueda).
