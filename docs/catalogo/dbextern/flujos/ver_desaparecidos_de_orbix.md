---
id: "dbextern.ver_desaparecidos_de_orbix.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - BDU sin ficha Aquinate"
capacidad: "dbextern.ver_desaparecidos_de_orbix.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_orbix_datos"]
estado_revision: "revisado"
---

# Flujo - BDU sin ficha Aquinate

Listado del punto 3.

## Objetivo De Usuario

Revisar personas BDU con vínculo pero sin ficha activa en esta DL.

## Punto De Entrada

**ver** del punto 3 en `sincro_index`.

## Escenarios

### Obtener datos

1. Envía `ids_desaparecidos_de_orbix` (JSON).
2. Tabla con acción **desunir**.

## Endpoints Del Flujo

- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Ruta de menú

- sin entrada de menú en el índice
