---
id: "pasarela.nombre_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Alta/edición nombre por tipo"
capacidad: "pasarela.nombre.gestionar"
pantallas_principales: ["pasarela.pantalla.nombre_ajax"]
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

Dentro de **nombres de actividades particulares**, con **nuevo** o abriendo una fila.

## Escenarios Inferidos

### Guardar o borrar el nombre de un tipo

Pasos propuestos:
1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el nombre.
3. Pulsar **guardar**, o **eliminar** si se quiere quitar. Sin tipo o sin nombre el sistema avisa de lo que falta.

Endpoints asociados:
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Endpoints Del Flujo

- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta nombre`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
