---
id: "cartaspresentacion.poblaciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Poblaciones"
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

Filtrar el listado de centros por población dentro de la delegación (modo `get_dl`).

## Punto De Entrada

Pantalla `cartas_presentacion`: al cambiar el desplegable «según dl» se invoca `fnjs_poblacion`.

## Escenarios

### Obtener poblaciones

1. Usuario elige `get_dl` en `tipo_lista`.
2. AJAX a `poblaciones_data` con `filtro=get_dl`.
3. Se reconstruye el desplegable `poblacion_sel` con `fnjs_construir_desplegable`.
4. Si elige `get_r`, se oculta el desplegable de población (sin llamada AJAX).

## Endpoints Del Flujo

- `/src/cartaspresentacion/poblaciones_data`

## Ruta de menú

sin entrada de menú en el índice (auxiliar de `cartas_presentacion` > modificar).
