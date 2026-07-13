---
id: "pasarela.contribucion_no_duerme_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Excepción contribución no duerme"
capacidad: "pasarela.contribucion_no_duerme.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.contribucion_no_duerme_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/contribucion_no_duerme_excepcion_guardar"
  - "\/src\/pasarela\/contribucion_no_duerme_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
estado_revision: "revisado"
---

# Flujo - Excepción contribución no duerme

## Objetivo De Usuario

Porcentaje distinto por tipo de actividad.

## Punto De Entrada

Listado contribución no duerme.

## Escenarios

Alta/edición/eliminación de fila de excepción.

## Endpoints Del Flujo

- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
