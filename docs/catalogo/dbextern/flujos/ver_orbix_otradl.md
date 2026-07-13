---
id: "dbextern.ver_orbix_otradl.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar traslados punto 7"
capacidad: "dbextern.ver_orbix_otradl.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_orbix_otradl"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_orbix_otradl_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar traslados punto 7

Personas Aquinate que deben trasladarse a la DL de su BDU.

## Objetivo De Usuario

Ver quién está activo aquí pero su correspondencia BDU pertenece a otra DL.

## Punto De Entrada

**ver** del punto 7 en `sincro_index`.

## Escenarios

### Obtener datos

1. Envía `ids_traslados_A` (JSON) y `tipo_persona`.
2. Tabla con nombre, DL destino y acción trasladar.

## Endpoints Del Flujo

- `/src/dbextern/ver_orbix_otradl_datos`

## Ruta de menú

- sin entrada de menú en el índice
