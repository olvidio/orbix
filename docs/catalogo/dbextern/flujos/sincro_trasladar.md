---
id: "dbextern.sincro_trasladar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Trasladar a esta DL"
capacidad: "dbextern.sincro_trasladar.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_traslados"]
acciones: ["trasladar"]
endpoints: ["/src/dbextern/sincro_trasladar"]
estado_revision: "revisado"
---

# Flujo - Trasladar a esta DL

Traslado individual desde otra DL Orbix (punto 2).

## Objetivo De Usuario

Traer la ficha a la DL actual; la fecha de traslado queda en hoy (aviso en pantalla).

## Punto De Entrada

**trasladar** en `ver_traslados` (`fnjs_trasladar`).

## Escenarios

### Trasladar

1. Alerta informativa sobre fecha.
2. POST `id_nom_orbix`, `dl` origen, `tipo_persona`.
3. Fila tachada si éxito.

## Endpoints Del Flujo

- `/src/dbextern/sincro_trasladar`

## Errores Conocidos

- Mensajes del dominio `Trasladar` / `Error al trasladar`

## Ruta de menú

- sin entrada de menú en el índice
