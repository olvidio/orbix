---
id: "dbextern.sincro_trasladar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Traer una ficha a esta dl"
capacidad: "dbextern.sincro_trasladar.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_traslados"]
fragmentos: ["dbextern.pantalla.ver_traslados"]
acciones: ["trasladar"]
endpoints: ["/src/dbextern/sincro_trasladar"]
estado_revision: "revisado"
---

# Flujo - Trasladar a esta DL

Traslado individual desde otra DL Orbix (punto 2).

## Objetivo De Usuario

Traer una ficha a la delegación actual. La fecha de traslado queda en el día de hoy.

## Punto De Entrada

Botón **trasladar** en la lista del punto 2 («Revisar traslados punto 2»).

## Escenarios Inferidos

### Traer una ficha a esta dl

Pasos propuestos:
1. En el punto 2, pulsar **trasladar** en la persona.
2. Aparece un aviso sobre la fecha del traslado (hoy); aceptarlo.
3. Si se ha hecho bien, la fila aparece tachada.

Endpoints asociados:
- `/src/dbextern/sincro_trasladar`

## Endpoints Del Flujo

- `/src/dbextern/sincro_trasladar`

## Errores Conocidos

- Mensajes del dominio `Trasladar` / `Error al trasladar`

## Ruta de menú

- sin entrada de menú en el índice
