---
id: "dbextern.sincro_baja.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Dar de baja una ficha de Aquinate"
capacidad: "dbextern.sincro_baja.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
acciones: ["baja"]
endpoints: ["/src/dbextern/sincro_baja"]
estado_revision: "revisado"
---

# Flujo - Baja ficha Aquinate

Da de baja personas del punto 8 (situación `B`).

## Objetivo De Usuario

Dar de baja la ficha de Aquinate de una persona que ya no está en la BDU.

## Punto De Entrada

Botón **baja** en la lista «Aquinate con BDU vacía» (punto 8 de la sincronización).

## Escenarios Inferidos

### Dar de baja una ficha de Aquinate

Pasos propuestos:
1. En la lista de fichas Aquinate sin correspondencia en la BDU, pulsar **baja** en la persona.
2. La ficha pasa a situación de baja. Si no se pudo cambiar la situación, avisa «OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.»

Endpoints asociados:
- `/src/dbextern/sincro_baja`

## Endpoints Del Flujo

- `/src/dbextern/sincro_baja`

## Errores Conocidos

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Ruta de menú

- sin entrada de menú en el índice
