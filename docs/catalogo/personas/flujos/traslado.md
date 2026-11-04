---
id: "personas.traslado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Trasladar a una persona"
capacidad: "personas.traslado.gestionar"
pantallas_principales: ["personas.pantalla.traslado_form"]
fragmentos: ["personas.pantalla.traslado_form"]
acciones: ["ver_formulario", "crear_actualizar"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
estado_revision: "revisado"
---

# Flujo - Trasladar persona

Cambio de centro y/o delegación con registro en dossier de traslados.

## Objetivo De Usuario

Cambiar a una persona de centro o de delegación, con fecha y situación.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl). Marcar una persona y pulsar **cambio de ctr**.

## Escenarios Inferidos

### Trasladar a una persona

Pasos propuestos:
1. Marcar una persona y pulsar **cambio de ctr**.
2. Se muestra una tabla Tipo / Origen / Destino / Fecha: en la fila **Centro** (origen, por ejemplo «Diagonal») elegir el centro de destino y la fecha; en la fila **Delegación** (origen, por ejemplo «dlb») elegir la delegación de destino y la fecha.
3. Elegir la **Situación** (por ejemplo «Alta») y pulsar **Actualizar cambios**.
4. Las personas de paso publicadas no se pueden trasladar.

Endpoints asociados:
- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Endpoints Del Flujo

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Errores Conocidos

- `con las personas de paso no tiene sentido.`
- `Falta una situación válida`
- `Faltan id_pau u obj_pau`

## Ruta de menú

- sin entrada de menú en el índice.
