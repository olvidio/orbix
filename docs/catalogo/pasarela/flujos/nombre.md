---
id: "pasarela.nombre.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar nombres particulares"
capacidad: "pasarela.nombre.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.nombre_ajax"
  - "pasarela.pantalla.nombre_form"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/nombre_lista"
  - "\/src\/pasarela\/nombre_excepcion_guardar"
  - "\/src\/pasarela\/nombre_excepcion_eliminar"
estado_revision: "revisado"
---

# Flujo - Gestionar nombres particulares

## Objetivo De Usuario

Asignar nombre exportado distinto al tipo genérico.

## Punto De Entrada

`nombre_lista.php` desde parámetros.

## Escenarios

1. Listar excepciones.
2. Añadir tipo + nombre o editar/eliminar fila.

## Endpoints Del Flujo

- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta nombre`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
