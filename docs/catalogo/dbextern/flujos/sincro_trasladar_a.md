---
id: "dbextern.sincro_trasladar_a.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Trasladar una ficha a otra dl"
capacidad: "dbextern.sincro_trasladar_a.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_orbix_otradl"]
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
acciones: ["trasladar"]
endpoints: ["/src/dbextern/sincro_trasladar_a"]
estado_revision: "revisado"
---

# Flujo - Trasladar a otra DL

Traslado hacia la DL de la BDU (punto 7).

## Objetivo De Usuario

Mover la ficha de Aquinate a la delegación donde la persona está en las listas de la BDU.

## Punto De Entrada

Botón **trasladar** en la lista del punto 7 («Revisar traslados punto 7»).

## Escenarios Inferidos

### Trasladar una ficha a otra dl

Pasos propuestos:
1. En el punto 7, pulsar **trasladar** en la persona.
2. Solo se puede si la región de destino es la misma que la actual; si no, avisa «Este traslado debe hacerse desde el dossier de traslados».

Endpoints asociados:
- `/src/dbextern/sincro_trasladar_a`

## Endpoints Del Flujo

- `/src/dbextern/sincro_trasladar_a`

## Errores Conocidos

- `No se encontró la delegación destino`
- `Este traslado debe hacerse desde el dossier de traslados`

## Ruta de menú

- sin entrada de menú en el índice
