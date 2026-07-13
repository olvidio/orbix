---
id: "dbextern.sincro_desunir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Desunir vínculo BDU"
capacidad: "dbextern.sincro_desunir.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
acciones: ["desunir"]
endpoints: ["/src/dbextern/sincro_desunir"]
estado_revision: "revisado"
---

# Flujo - Desunir vínculo BDU

Elimina `id_match` para personas del punto 3.

## Objetivo De Usuario

Romper el vínculo incorrecto para poder re-unir o crear la ficha después.

## Punto De Entrada

**desunir** en `ver_desaparecidos_de_orbix` (`fnjs_desunir`).

## Escenarios

### Desunir

1. POST `id_nom_listas`, `tipo_persona`.
2. Fila tachada si éxito.

## Endpoints Del Flujo

- `/src/dbextern/sincro_desunir`

## Errores Conocidos

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

## Ruta de menú

- sin entrada de menú en el índice
