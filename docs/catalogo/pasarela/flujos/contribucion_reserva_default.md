---
id: "pasarela.contribucion_reserva_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Default contribución reserva"
capacidad: "pasarela.contribucion_reserva.gestionar"
pantallas_principales: []
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

Cambiar porcentaje global de reserva.

## Punto De Entrada

Listado contribución reserva.

## Escenarios

Form default + guardar.

## Endpoints Del Flujo

- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`

## Errores Conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
