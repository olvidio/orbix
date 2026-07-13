---
id: "personas.stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Guardar nivel STGR"
capacidad: "personas.stgr.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
acciones: ["crear_actualizar"]
endpoints: ["/src/personas/stgr_update"]
estado_revision: "revisado"
---

# Flujo - Guardar nivel STGR

Aplica el nuevo nivel STGR seleccionado en el formulario modal.

## Objetivo De Usuario

Actualizar el nivel STGR de una persona del listado.

## Punto De Entrada

- `stgr_cambio.phtml` → `fnjs_guardar_stgr`.

## Escenarios

### Guardar cambio

1. Elegir nivel en el desplegable.
2. Guardar → `stgr_update` con `id_nom`, `id_tabla`, `nivel_stgr`.
3. Volver al listado y comprobar columna STGR.

## Endpoints Del Flujo

- `/src/personas/stgr_update`

## Errores Conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `hay un error, no se ha guardado`

## Ruta de menú

- sin entrada de menú en el índice.
