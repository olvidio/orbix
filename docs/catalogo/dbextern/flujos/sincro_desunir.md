---
id: "dbextern.sincro_desunir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Desunir una persona de la BDU"
capacidad: "dbextern.sincro_desunir.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_orbix"]
acciones: ["desunir"]
endpoints: ["/src/dbextern/sincro_desunir"]
estado_revision: "revisado"
---

# Flujo - Desunir vínculo BDU

Elimina `id_match` para personas del punto 3.

## Objetivo De Usuario

Romper un vínculo incorrecto entre una persona de la BDU y una ficha de Aquinate, para poder unirla de nuevo o crear la ficha después.

## Punto De Entrada

Botón **desunir** en la lista «BDU sin ficha Aquinate» (punto 3).

## Escenarios Inferidos

### Desunir una persona de la BDU

Pasos propuestos:
1. En la lista «BDU sin ficha Aquinate», pulsar **desunir** en la persona.
2. Si se ha hecho bien, la fila aparece tachada.

Endpoints asociados:
- `/src/dbextern/sincro_desunir`

## Endpoints Del Flujo

- `/src/dbextern/sincro_desunir`

## Errores Conocidos

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

## Ruta de menú

- sin entrada de menú en el índice
