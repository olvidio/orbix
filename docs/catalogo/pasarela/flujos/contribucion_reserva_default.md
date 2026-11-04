---
id: "pasarela.contribucion_reserva_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Default contribución reserva"
capacidad: "pasarela.contribucion_reserva.gestionar"
pantallas_principales: ["pasarela.pantalla.contribucion_reserva_ajax"]
fragmentos:
  - "pasarela.pantalla.contribucion_reserva_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/contribucion_reserva_default_data"
  - "\/src\/pasarela\/contribucion_reserva_default_guardar"
estado_revision: "revisado"
---

# Flujo - Default contribución reserva

## Objetivo De Usuario

Cambiar el importe general de reserva.

## Punto De Entrada

Dentro de **Contribución reserva**, con el botón de modificar el valor por defecto.

## Escenarios Inferidos

### Cambiar el importe general

Pasos propuestos:
1. Abrir el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar** (o **cancel**). Fuera de rango o vacío, el sistema avisa.

Endpoints asociados:
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`

## Endpoints Del Flujo

- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`

## Errores Conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
