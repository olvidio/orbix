---
id: "dbextern.sincro_baja.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Baja ficha Aquinate"
capacidad: "dbextern.sincro_baja.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_desaparecidos_de_listas"]
acciones: ["baja"]
endpoints: ["/src/dbextern/sincro_baja"]
estado_revision: "revisado"
---

# Flujo - Baja ficha Aquinate

Da de baja personas del punto 8 (situación `B`).

## Objetivo De Usuario

Cerrar la ficha Aquinate cuando la persona ya no está en la BDU.

## Punto De Entrada

**baja** en `ver_desaparecidos_de_listas` (`fnjs_baja`).

## Escenarios

### Baja

1. POST `id_nom_orbix`, `tipo_persona`, `dl`.
2. Usa dominio `Trasladar` con situación `B`.

## Endpoints Del Flujo

- `/src/dbextern/sincro_baja`

## Errores Conocidos

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Ruta de menú

- sin entrada de menú en el índice
