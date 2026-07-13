---
id: "dbextern.ver_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar BDU no unidas"
capacidad: "dbextern.ver_listas.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["obtener_datos", "navegar"]
endpoints: ["/src/dbextern/ver_listas_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar BDU no unidas

Exploración del punto 4: cola de personas BDU sin vínculo.

## Objetivo De Usuario

Revisar una a una las personas de la BDU no unidas, viendo posibles coincidencias en Aquinate.

## Punto De Entrada

**ver** del punto 4 en `sincro_index` → `ver_listas.php`.

## Escenarios

### Obtener datos

1. Primera carga: `ver_listas_datos` con `first_load=1`, uniones automáticas, sesión `DBListas`.
2. Por cada registro: `ver_listas_datos` con `id_nom_bdu` para candidatos Orbix.

### Navegar

1. Botones anterior/siguiente actualizan `id` en sesión vía POST al mismo controller.

## Endpoints Del Flujo

- `/src/dbextern/ver_listas_datos`

## Ruta de menú

- sin entrada de menú en el índice
