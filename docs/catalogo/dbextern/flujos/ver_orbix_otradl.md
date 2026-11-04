---
id: "dbextern.ver_orbix_otradl.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar los traslados del punto 7"
capacidad: "dbextern.ver_orbix_otradl.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_orbix_otradl"]
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_orbix_otradl_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar traslados punto 7

Personas Aquinate que deben trasladarse a la DL de su BDU.

## Objetivo De Usuario

Ver quién está activo en esta delegación pero tiene su correspondencia en la BDU en otra delegación.

## Punto De Entrada

Enlace **ver** del punto 7 de la pantalla de sincronización.

## Escenarios Inferidos

### Revisar los traslados del punto 7

Pasos propuestos:
1. Pulsar **ver** en el punto 7.
2. Aparece la tabla con el nombre, la delegación de destino y la acción **trasladar**.

Endpoints asociados:
- `/src/dbextern/ver_orbix_otradl_datos`

## Endpoints Del Flujo

- `/src/dbextern/ver_orbix_otradl_datos`

## Ruta de menú

- sin entrada de menú en el índice
