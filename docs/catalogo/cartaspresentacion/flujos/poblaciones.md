---
id: "cartaspresentacion.poblaciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Elegir la población"
capacidad: "cartaspresentacion.poblaciones.gestionar"
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/cartaspresentacion/poblaciones_data"]
estado_revision: "revisado"
---

# Flujo - Desplegable de poblaciones

Recarga del desplegable de población al cambiar el filtro dl/regiones en la pantalla principal.

## Objetivo De Usuario

Filtrar la lista de centros por población dentro de la delegación.

## Punto De Entrada

Desplegable «según dl» de la pantalla de modificar cartas de presentación.

## Escenarios Inferidos

### Elegir la población

Pasos propuestos:
1. En «según dl» elegir la opción de la delegación: se rellena el desplegable de poblaciones.
2. Elegir la población. Si se elige «según r», el desplegable de población se oculta.

Endpoints asociados:
- `/src/cartaspresentacion/poblaciones_data`

## Endpoints Del Flujo

- `/src/cartaspresentacion/poblaciones_data`

## Ruta de menú

sin entrada de menú en el índice (auxiliar de `cartas_presentacion` > modificar).
