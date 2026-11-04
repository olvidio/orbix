---
id: "pasarela.nombre.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar nombres particulares"
capacidad: "pasarela.nombre.gestionar"
pantallas_principales: ["pasarela.pantalla.nombre_lista"]
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

Menú **Pasarela > parámetros > nombres de actividades particulares**.

## Escenarios Inferidos

### Consultar los nombres particulares

Pasos propuestos:
1. Abrir **Pasarela > parámetros** y pulsar **nombres de actividades particulares**.
2. Leer la lista de tipos de actividad con nombre propio.

Endpoints asociados:
- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

### Poner un nombre a un tipo de actividad

Pasos propuestos:
1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el **nombre actividad** que se exportará.
3. Pulsar **guardar**.

Endpoints asociados:
- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

### Cambiar o quitar un nombre

Pasos propuestos:
1. Abrir la fila del tipo.
2. Cambiar el nombre y pulsar **guardar**, o pulsar **eliminar** y confirmar «¿Está seguro que desea borrar este nombre?».

Endpoints asociados:
- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

## Endpoints Del Flujo

- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta nombre`

## Ruta de menú

- **Legacy:** dre > Pasarela > parámetros > nombres de actividades particulares
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > nombres de actividades particulares
