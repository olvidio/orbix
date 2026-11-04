---
id: "dbextern.sincro_unir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Unir una persona de la BDU con una de Aquinate"
capacidad: "dbextern.sincro_unir.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_listas", "dbextern.pantalla.ver_orbix"]
fragmentos: ["dbextern.pantalla.ver_listas", "dbextern.pantalla.ver_orbix"]
acciones: ["unir"]
endpoints: ["/src/dbextern/sincro_unir"]
estado_revision: "revisado"
---

# Flujo - Unir BDU con Aquinate

Vinculación manual entre IDs BDU y Aquinate.

## Objetivo De Usuario

Confirmar la correspondencia entre una persona de la BDU y una de Aquinate cuando el sistema propone candidatos (puntos 4 y 9).

## Punto De Entrada

Botón **unir** en «Revisar BDU no unidas» (candidato de Aquinate para una persona de la BDU) o en «Revisar Aquinate sin BDU» (candidato de la BDU para una persona de Aquinate).

## Escenarios Inferidos

### Unir una persona de la BDU con una de Aquinate

Pasos propuestos:
1. En la pantalla de revisión, comprobar que el candidato propuesto es la misma persona.
2. Pulsar **unir**.
3. La persona sale de la cola y pasa a la siguiente.

Endpoints asociados:
- `/src/dbextern/sincro_unir`

## Endpoints Del Flujo

- `/src/dbextern/sincro_unir`

## Errores Conocidos

- `hay un error, no se ha guardado`

## Ruta de menú

- sin entrada de menú en el índice
