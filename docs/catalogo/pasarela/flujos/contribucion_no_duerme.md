---
id: "pasarela.contribucion_no_duerme.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar contribución no duerme"
capacidad: "pasarela.contribucion_no_duerme.gestionar"
pantallas_principales: ["pasarela.pantalla.contribucion_no_duerme_lista"]
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

Importe de contribución para quien no pernocta.

## Punto De Entrada

Menú **Pasarela > parámetros > Contribución no duerme**.

## Escenarios Inferidos

### Consultar el importe

Pasos propuestos:
1. Abrir **Pasarela > parámetros** y pulsar **Contribución no duerme**.
2. Leer el importe general y las excepciones por tipo de actividad.

Endpoints asociados:
- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

### Cambiar el importe general

Pasos propuestos:
1. Pulsar el botón de modificar el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar**.

Endpoints asociados:
- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

### Poner un importe distinto a un tipo de actividad

Pasos propuestos:
1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**; con **eliminar** la fila el tipo vuelve al importe general.

Endpoints asociados:
- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

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

- **Legacy:** dre > Pasarela > parámetros > Contribución no duerme
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > Contribución no duerme
