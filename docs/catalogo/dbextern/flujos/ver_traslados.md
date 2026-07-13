---
id: "dbextern.ver_traslados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar traslados punto 2"
capacidad: "dbextern.ver_traslados.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_traslados"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_traslados_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar traslados punto 2

Listado de personas unidas a BDU pero en otra DL Orbix.

## Objetivo De Usuario

Identificar quién debe trasladarse a esta DL desde otra delegación.

## Punto De Entrada

**ver** del punto 2 en `sincro_index`.

## Escenarios

### Obtener datos

1. Envía `ids_traslados` (JSON) y `tipo_persona`.
2. Muestra tabla con nombre, DL actual y acción trasladar.

## Endpoints Del Flujo

- `/src/dbextern/ver_traslados_datos`

## Errores Conocidos

- `No existe la clase de la persona`

## Ruta de menú

- sin entrada de menú en el índice
