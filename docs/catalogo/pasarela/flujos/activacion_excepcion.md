---
id: "pasarela.activacion_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Excepción de activación por tipo"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.activacion_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/activacion_excepcion_guardar"
  - "\/src\/pasarela\/activacion_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
estado_revision: "revisado"
---

# Flujo - Excepción de activación por tipo

## Objetivo De Usuario

Definir activación distinta para un tipo de actividad concreto.

## Punto De Entrada

Listado activación → nueva fila o editar.

## Escenarios

1. Elegir tipo (`ActividadTipo`).
2. Indicar activación.
3. Guardar o eliminar fila.

## Endpoints Del Flujo

- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta valor de activación`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
