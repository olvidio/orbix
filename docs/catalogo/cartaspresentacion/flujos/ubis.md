---
id: "cartaspresentacion.ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Ubis"
capacidad: "cartaspresentacion.ubis.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
estado_revision: "revisado"
---

# Flujo - Listado de centros con estado de carta

Tabla interactiva de centros y su carta de presentación (sí/no).

## Objetivo De Usuario

Ver qué centros tienen carta de presentación y acceder a modificar, ver ficha o quitar.

## Punto De Entrada

Pantalla `cartas_presentacion`: tras pulsar **buscar**, se carga `cartas_presentacion_ubis_lista.php`
en `#ficha2`.

## Escenarios

### Listar

1. Elegir filtro dl/regiones (+ población si `get_dl`).
2. Pulsar **buscar** → `ubis_lista_data` con `tipo_lista` y `poblacion_sel`.
3. Tabla con columnas director, centro, carta de presentación, dirección.

## Endpoints Del Flujo

- `/src/cartaspresentacion/ubis_lista_data`

## Ruta de menú

sin entrada de menú en el índice (fragmento de `cartas_presentacion` > modificar).
