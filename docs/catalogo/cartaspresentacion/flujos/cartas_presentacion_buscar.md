---
id: "cartaspresentacion.cartas_presentacion_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Buscar cartas de presentación"
capacidad: "cartaspresentacion.cartas_presentacion_buscar.gestionar"
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion_buscar"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
acciones: ["buscar"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data", "/src/cartaspresentacion/cartas_presentacion_lista_data"]
estado_revision: "revisado"
---

# Flujo - Buscar cartas de presentación

Búsqueda filtrada por población, región, país y delegación.

## Objetivo De Usuario

Encontrar cartas de presentación según criterios geográficos o de delegación.

## Punto De Entrada

Menú **scdl > direcciones > cartas presentacion > buscar**.

## Escenarios Inferidos

### Buscar cartas de presentación

Pasos propuestos:
1. Abrir **buscar**: se cargan los desplegables de filtro.
2. Rellenar los filtros y pulsar **buscar**.
3. Los resultados aparecen debajo, en el mismo formato que las listas.

Endpoints asociados:
- `/src/cartaspresentacion/cartas_presentacion_buscar_data`
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_buscar_data`
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > buscar
- **Pills2:** scdl > direcciones > cartas presentacion > buscar
