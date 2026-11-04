---
id: "dbextern.sincro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Crear una persona desde la BDU"
capacidad: "dbextern.sincro.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_listas"]
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["crear"]
endpoints: ["/src/dbextern/sincro_crear"]
estado_revision: "revisado"
---

# Flujo - Crear persona desde BDU

Alta individual de ficha Aquinate desde la BDU (punto 4).

## Objetivo De Usuario

Cuando una persona de la BDU no tiene coincidencia en Aquinate, crear una ficha nueva y vincularla automáticamente.

## Punto De Entrada

Botón **crear nuevo** en «Revisar BDU no unidas».

## Escenarios Inferidos

### Crear una persona desde la BDU

Pasos propuestos:
1. En «Revisar BDU no unidas», con la persona delante, pulsar **crear nuevo**.
2. Se crea la ficha y queda vinculada.
3. Pasa automáticamente a la siguiente persona de la lista.

Endpoints asociados:
- `/src/dbextern/sincro_crear`

## Endpoints Del Flujo

- `/src/dbextern/sincro_crear`

## Errores Conocidos

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `hay un error, no se ha guardado`

## Ruta de menú

- sin entrada de menú en el índice
