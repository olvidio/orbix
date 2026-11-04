---
id: "cartaspresentacion.cartas_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Ver el listado de cartas"
capacidad: "cartaspresentacion.cartas_presentacion.gestionar"
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
estado_revision: "revisado"
---

# Flujo - Consultar listado de cartas de presentación

Visualización del listado agrupado de cartas (solo lectura) desde el menú o desde buscar.

## Objetivo De Usuario

Consultar todas las cartas de presentación agrupadas por tipo de labor, delegación y población.

## Punto De Entrada

Menú **scdl > direcciones > cartas presentacion**: **lista dl** (solo la propia delegación) o **lista todo** (todas). La pantalla **buscar** muestra el mismo listado con filtros.

## Escenarios Inferidos

### Ver el listado de cartas

Pasos propuestos:
1. Abrir **lista dl** o **lista todo**.
2. Aparecen las tablas agrupadas con los datos de contacto.
3. Los centros con el tipo de labor mal configurado salen en un aviso al final de la página.

Endpoints asociados:
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

### Ver el listado filtrado

Pasos propuestos:
1. Abrir **buscar**, rellenar los filtros y pulsar **buscar**.
2. Los resultados aparecen debajo.

Endpoints asociados:
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Errores Conocidos

- Centros con `tipo_labor` mal configurado aparecen en aviso al pie (`html_errores`), no como error AJAX.

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > lista dl / lista todo
- **Pills2:** scdl > direcciones > cartas presentacion > lista dl / lista todo
