---
id: "dbextern.sincro_unir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Unir BDU con Aquinate"
capacidad: "dbextern.sincro_unir.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas", "dbextern.pantalla.ver_orbix"]
acciones: ["unir"]
endpoints: ["/src/dbextern/sincro_unir"]
estado_revision: "revisado"
---

# Flujo - Unir BDU con Aquinate

Vinculación manual entre IDs BDU y Aquinate.

## Objetivo De Usuario

Confirmar la correspondencia cuando el sistema sugiere candidatos (puntos 4 y 9).

## Punto De Entrada

- **unir** en `ver_listas` (`fnjs_unir`) — candidato Orbix para persona BDU
- **unir** en `ver_orbix` (`fnjs_unir_bdu`) — candidato BDU para persona Aquinate

## Escenarios

### Unir

1. Envía `id_nom_listas`, `id_orbix`, `tipo_persona`, `id` de sesión.
2. Tras éxito, elimina fila de sesión y avanza.

## Endpoints Del Flujo

- `/src/dbextern/sincro_unir`

## Errores Conocidos

- `hay un error, no se ha guardado`

## Ruta de menú

- sin entrada de menú en el índice
