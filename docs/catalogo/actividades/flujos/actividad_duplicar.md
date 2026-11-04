---
id: "actividades.actividad_duplicar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Duplicar una actividad"
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

Crear una copia de una actividad con las mismas fechas, plazas y demás datos.

## Punto De Entrada

Lista de resultados de **Buscar actividad** (ACTIVIDADES > Buscar actividad, o la entrada del colectivo).

## Escenarios Inferidos

### Duplicar una actividad

Pasos propuestos:
1. Marcar la actividad en la lista y pulsar **duplicar**.
2. Confirmar el aviso «Seguro que desea duplicar esta actividad».
3. La copia se llama «dup » + el nombre original y conserva fechas y plazas; la lista se vuelve a cargar con la búsqueda por defecto, así que hay que buscar de nuevo para ver la copia.

Endpoints asociados:
- `/src/actividades/actividad_duplicar`

## Endpoints Del Flujo

- `/src/actividades/actividad_duplicar`

## Errores Conocidos

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`
- `hay un error, no se ha guardado` + detalle

## Ruta de menú

sin entrada de menú en el índice (acción desde listado de búsqueda).
