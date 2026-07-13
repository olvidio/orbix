---
id: "dbextern.sincro_trasladar_a.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Trasladar a otra DL"
capacidad: "dbextern.sincro_trasladar_a.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
acciones: ["trasladar"]
endpoints: ["/src/dbextern/sincro_trasladar_a"]
estado_revision: "revisado"
---

# Flujo - Trasladar a otra DL

Traslado hacia la DL de la BDU (punto 7).

## Objetivo De Usuario

Mover la ficha Aquinate a la delegación donde está su correspondencia en listas.

## Punto De Entrada

**trasladar** en `ver_orbix_otradl`.

## Escenarios

### Trasladar

1. POST `id_nom_orbix`, `dl` destino, `tipo_persona`.
2. Solo permitido si región destino = región actual.

## Endpoints Del Flujo

- `/src/dbextern/sincro_trasladar_a`

## Errores Conocidos

- `No se encontró la delegación destino`
- `Este traslado debe hacerse desde el dossier de traslados`

## Ruta de menú

- sin entrada de menú en el índice
