---
id: "dbextern.ver_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar las personas de la BDU no unidas"
capacidad: "dbextern.ver_listas.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_listas"]
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["obtener_datos", "navegar"]
endpoints: ["/src/dbextern/ver_listas_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar BDU no unidas

Exploración del punto 4: cola de personas BDU sin vínculo.

## Objetivo De Usuario

Revisar una a una las personas de la BDU que no están unidas a ninguna ficha, viendo las posibles coincidencias en Aquinate.

## Punto De Entrada

Enlace **ver** del punto 4 de la pantalla de sincronización.

## Escenarios Inferidos

### Revisar las personas de la BDU no unidas

Pasos propuestos:
1. Pulsar **ver** en el punto 4: en la primera carga se hacen las uniones automáticas evidentes.
2. Aparece la primera persona pendiente con sus candidatos de Aquinate.
3. Elegir **unir** con un candidato o **crear nuevo** si no hay coincidencia.
4. Usar los botones anterior y siguiente para moverse por la lista.

Endpoints asociados:
- `/src/dbextern/ver_listas_datos`

## Endpoints Del Flujo

- `/src/dbextern/ver_listas_datos`

## Ruta de menú

- sin entrada de menú en el índice
