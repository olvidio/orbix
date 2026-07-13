---
id: "pasarela.contribucion_no_duerme.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar contribución no duerme"
capacidad: "pasarela.contribucion_no_duerme.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.contribucion_no_duerme_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/contribucion_no_duerme_lista"
  - "\/src\/pasarela\/contribucion_no_duerme_default_data"
  - "\/src\/pasarela\/contribucion_no_duerme_default_guardar"
  - "\/src\/pasarela\/contribucion_no_duerme_excepcion_guardar"
  - "\/src\/pasarela\/contribucion_no_duerme_excepcion_eliminar"
estado_revision: "revisado"
---

# Flujo - Gestionar contribución no duerme

## Objetivo De Usuario

Porcentaje de contribución para quien no pernocta.

## Punto De Entrada

`contribucion_no_duerme_lista.php` desde parámetros.

## Escenarios

Listar, editar default (0–100 %), alta/edición/eliminación de excepciones.

## Endpoints Del Flujo

- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

## Errores Conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
