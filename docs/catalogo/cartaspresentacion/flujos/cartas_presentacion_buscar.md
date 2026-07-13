---
id: "cartaspresentacion.cartas_presentacion_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Cartas Presentacion Buscar"
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

Encontrar cartas de presentación que cumplan criterios geográficos o de delegación.

## Punto De Entrada

Pantalla `cartas_presentacion_buscar` — menú scdl > direcciones > cartas presentacion > buscar.

## Escenarios

### Buscar

1. Al abrir, se cargan desplegables (`cartas_presentacion_buscar_data`).
2. Rellenar filtros y pulsar **buscar**.
3. `cartas_presentacion_lista.php` invoca `cartas_presentacion_lista_data` con `que=get`.
4. Resultados en `#resultados`.

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_buscar_data`
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > buscar
- **Pills2:** scdl > direcciones > cartas presentacion > buscar
