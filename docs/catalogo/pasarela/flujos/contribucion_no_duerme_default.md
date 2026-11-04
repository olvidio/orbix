---
id: "pasarela.contribucion_no_duerme_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Default contribución no duerme"
capacidad: "pasarela.contribucion_no_duerme.gestionar"
pantallas_principales: ["pasarela.pantalla.contribucion_no_duerme_ajax"]
fragmentos:
  - "pasarela.pantalla.contribucion_no_duerme_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/contribucion_no_duerme_default_data"
  - "\/src\/pasarela\/contribucion_no_duerme_default_guardar"
estado_revision: "revisado"
---

# Flujo - Default contribución no duerme

## Objetivo De Usuario

Cambiar el importe global.

## Punto De Entrada

Dentro de **Contribución no duerme**, con el botón de modificar el valor por defecto.

## Escenarios Inferidos

### Cambiar el importe general

Pasos propuestos:
1. Abrir el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar** (o **cancel**). Fuera de rango o vacío, el sistema avisa.

Endpoints asociados:
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`

## Endpoints Del Flujo

- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`

## Errores Conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
