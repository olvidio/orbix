---
id: "dbextern.ver_desaparecidos_de_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar fichas Aquinate con la BDU vacía"
capacidad: "dbextern.ver_desaparecidos_de_listas.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_listas_datos"]
estado_revision: "revisado"
---

# Flujo - Aquinate con BDU vacía

Listado del punto 8.

## Objetivo De Usuario

Revisar las fichas de Aquinate cuya correspondencia en la BDU ya no existe.

## Punto De Entrada

Enlace **ver** del punto 8 de la pantalla de sincronización.

## Escenarios Inferidos

### Revisar fichas Aquinate con la BDU vacía

Pasos propuestos:
1. Pulsar **ver** en el punto 8.
2. Aparece la tabla de personas, con la acción **baja** y un enlace para hacer el traslado externo.

Endpoints asociados:
- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Endpoints Del Flujo

- `/src/dbextern/ver_desaparecidos_de_listas_datos`

## Ruta de menú

- sin entrada de menú en el índice
