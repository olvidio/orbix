---
id: "pasarela.contribucion_no_duerme_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Default contribución no duerme"
capacidad: "pasarela.contribucion_no_duerme.gestionar"
pantallas_principales: []
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

Cambiar el porcentaje global.

## Punto De Entrada

Listado contribución no duerme.

## Escenarios

Carga formulario y guarda porcentaje default.

## Endpoints Del Flujo

- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`

## Errores Conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
