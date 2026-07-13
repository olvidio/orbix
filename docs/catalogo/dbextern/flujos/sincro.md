---
id: "dbextern.sincro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Crear persona desde BDU"
capacidad: "dbextern.sincro.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["crear"]
endpoints: ["/src/dbextern/sincro_crear"]
estado_revision: "revisado"
---

# Flujo - Crear persona desde BDU

Alta individual de ficha Aquinate desde la BDU (punto 4).

## Objetivo De Usuario

Cuando no hay coincidencia Orbix, crear una ficha nueva y vincularla automáticamente.

## Punto De Entrada

Botón **crear nuevo** en `ver_listas` (`fnjs_crear`).

## Escenarios

### Crear

1. Envía `id_nom_listas`, `tipo_persona`, `id` (índice en sesión).
2. Tras éxito, avanza al siguiente registro (`fnjs_submit` con `-`).

## Endpoints Del Flujo

- `/src/dbextern/sincro_crear`

## Errores Conocidos

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `hay un error, no se ha guardado`

## Ruta de menú

- sin entrada de menú en el índice
