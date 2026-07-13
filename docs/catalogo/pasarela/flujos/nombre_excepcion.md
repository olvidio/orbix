---
id: "pasarela.nombre_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Alta/edición nombre por tipo"
capacidad: "pasarela.nombre.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.nombre_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/nombre_excepcion_guardar"
  - "\/src\/pasarela\/nombre_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
estado_revision: "revisado"
---

# Flujo - Alta/edición nombre por tipo

## Objetivo De Usuario

Guardar o borrar un nombre concreto.

## Punto De Entrada

Formulario desde listado nombre.

## Escenarios

Seleccionar tipo, escribir nombre, guardar o eliminar.

## Endpoints Del Flujo

- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta nombre`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
