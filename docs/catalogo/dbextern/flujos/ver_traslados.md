---
id: "dbextern.ver_traslados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar los traslados del punto 2"
capacidad: "dbextern.ver_traslados.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_traslados"]
fragmentos: ["dbextern.pantalla.ver_traslados"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_traslados_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar traslados punto 2

Listado de personas unidas a BDU pero en otra DL Orbix.

## Objetivo De Usuario

Identificar a quién hay que trasladar a esta delegación desde otra.

## Punto De Entrada

Enlace **ver** del punto 2 de la pantalla de sincronización.

## Escenarios Inferidos

### Revisar los traslados del punto 2

Pasos propuestos:
1. Pulsar **ver** en el punto 2.
2. Aparece la tabla con el nombre, la delegación actual y la acción **trasladar**.

Endpoints asociados:
- `/src/dbextern/ver_traslados_datos`

## Endpoints Del Flujo

- `/src/dbextern/ver_traslados_datos`

## Errores Conocidos

- `No existe la clase de la persona`

## Ruta de menú

- sin entrada de menú en el índice
