---
id: "dbextern.ver_orbix.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar las personas de Aquinate sin BDU"
capacidad: "dbextern.ver_orbix.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_orbix"]
fragmentos: ["dbextern.pantalla.ver_orbix"]
acciones: ["obtener_datos", "navegar"]
endpoints: ["/src/dbextern/ver_orbix_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar Aquinate sin BDU

Exploración del punto 9.

## Objetivo De Usuario

Revisar las personas activas de Aquinate sin correspondencia en la BDU y unirlas si hay candidato.

## Punto De Entrada

Enlace **ver** del punto 9 de la pantalla de sincronización.

## Escenarios Inferidos

### Revisar las personas de Aquinate sin BDU

Pasos propuestos:
1. Pulsar **ver** en el punto 9.
2. Aparece la primera persona con sus posibles candidatos de la BDU.
3. Pulsar **unir** si hay coincidencia.
4. Usar anterior y siguiente para recorrer la lista.

Endpoints asociados:
- `/src/dbextern/ver_orbix_datos`

## Endpoints Del Flujo

- `/src/dbextern/ver_orbix_datos`

## Errores Conocidos

- `No existe la clase de la persona`

## Ruta de menú

- sin entrada de menú en el índice
