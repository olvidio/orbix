---
id: "dbextern.ver_desaparecidos_de_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Aquinate con BDU vacía"
capacidad: "dbextern.ver_desaparecidos_de_listas.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_listas_datos"]
estado_revision: "revisado"
---

# Flujo - Aquinate con BDU vacía

Listado del punto 8.

## Objetivo De Usuario

Revisar fichas Aquinate cuya correspondencia BDU ya no existe.

## Punto De Entrada

**ver** del punto 8 en `sincro_index`.

## Escenarios

### Obtener datos

1. Envía `ids_desaparecidos_de_listas` (JSON).
2. Tabla con acciones **baja** y enlace a traslado externo (`fnjs_traslado`).

## Endpoints Del Flujo

- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Ruta de menú

- sin entrada de menú en el índice
