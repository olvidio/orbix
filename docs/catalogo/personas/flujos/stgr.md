---
id: "personas.stgr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Guardar el nivel stgr"
capacidad: "personas.stgr.gestionar"
pantallas_principales: ["personas.pantalla.stgr_cambio"]
fragmentos: ["personas.pantalla.stgr_cambio"]
acciones: ["crear_actualizar"]
endpoints: ["/src/personas/stgr_update"]
estado_revision: "revisado"
---

# Flujo - Guardar nivel STGR

Aplica el nuevo nivel STGR seleccionado en el formulario modal.

## Objetivo De Usuario

Guardar el nivel de estudios (stgr) elegido para una persona.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl). **modificar stgr**.

## Escenarios Inferidos

### Guardar el nivel stgr

Pasos propuestos:
1. Elegir el nivel en el desplegable y pulsar **Guardar**.
2. No sale mensaje: se vuelve a la lista de personas. El nuevo nivel se ve en la ficha de la persona (campo «stgr»).

Endpoints asociados:
- `/src/personas/stgr_update`

## Endpoints Del Flujo

- `/src/personas/stgr_update`

## Errores Conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `hay un error, no se ha guardado`

## Ruta de menú

- sin entrada de menú en el índice.
