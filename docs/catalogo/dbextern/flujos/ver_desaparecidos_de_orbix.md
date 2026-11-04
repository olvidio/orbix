---
id: "dbextern.ver_desaparecidos_de_orbix.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar personas de la BDU sin ficha Aquinate"
capacidad: "dbextern.ver_desaparecidos_de_orbix.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_orbix_datos"]
estado_revision: "revisado"
---

# Flujo - BDU sin ficha Aquinate

Listado del punto 3.

## Objetivo De Usuario

Revisar las personas de la BDU que tienen vínculo pero ya no tienen ficha activa en esta delegación.

## Punto De Entrada

Enlace **ver** del punto 3 de la pantalla de sincronización.

## Escenarios Inferidos

### Revisar personas de la BDU sin ficha Aquinate

Pasos propuestos:
1. Pulsar **ver** en el punto 3.
2. Aparece la tabla de personas con la acción **desunir**.

Endpoints asociados:
- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Endpoints Del Flujo

- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Ruta de menú

- sin entrada de menú en el índice
