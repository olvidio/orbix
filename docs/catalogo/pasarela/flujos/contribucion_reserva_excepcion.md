---
id: "pasarela.contribucion_reserva_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Excepción contribución reserva"
capacidad: "pasarela.contribucion_reserva.gestionar"
pantallas_principales: ["pasarela.pantalla.contribucion_reserva_ajax"]
fragmentos:
  - "pasarela.pantalla.contribucion_reserva_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/contribucion_reserva_excepcion_guardar"
  - "\/src\/pasarela\/contribucion_reserva_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
estado_revision: "revisado"
---

# Flujo - Excepción contribución reserva

## Objetivo De Usuario

Importe de reserva distinto por tipo.

## Punto De Entrada

Dentro de **Contribución reserva**, con **nuevo** o abriendo una fila.

## Escenarios Inferidos

### Añadir, cambiar o quitar un importe por tipo

Pasos propuestos:
1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**, o **eliminar** para quitar la excepción.

Endpoints asociados:
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Endpoints Del Flujo

- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
