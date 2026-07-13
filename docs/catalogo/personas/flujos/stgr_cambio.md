---
id: "personas.stgr_cambio.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Cambiar nivel STGR (formulario)"
capacidad: "personas.stgr_cambio.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/stgr_cambio_data"]
estado_revision: "revisado"
---

# Flujo - Cambiar nivel STGR (formulario)

Apertura del formulario de cambio de nivel STGR desde el listado.

## Objetivo De Usuario

Ver el nivel actual y las opciones disponibles antes de guardar el cambio.

## Punto De Entrada

- Listado `personas_select`: botón «modificar stgr» (`have_perm_oficina('est')`).

## Escenarios

### Abrir formulario

1. Seleccionar exactamente una persona.
2. Pulsar «modificar stgr».
3. `stgr_cambio_data` devuelve nombre, nivel actual y opciones.

La persistencia es el flujo `stgr` (`stgr_update`).

## Endpoints Del Flujo

- `/src/personas/stgr_cambio_data`

## Errores Conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Ruta de menú

- sin entrada de menú en el índice.
