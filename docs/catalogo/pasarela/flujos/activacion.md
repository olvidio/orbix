---
id: "pasarela.activacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar fecha de activación"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: ["pasarela.pantalla.activacion_lista"]
fragmentos:
  - "pasarela.pantalla.activacion_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/activacion_lista"
  - "\/src\/pasarela\/activacion_default_data"
  - "\/src\/pasarela\/activacion_default_guardar"
  - "\/src\/pasarela\/activacion_excepcion_guardar"
  - "\/src\/pasarela\/activacion_excepcion_eliminar"
estado_revision: "revisado"
---

# Flujo - Gestionar fecha de activación

## Objetivo De Usuario

Configurar cuándo se publica/activa cada tipo de actividad en la pasarela exterior.

## Punto De Entrada

Menú **Pasarela > parámetros > fecha de activación**. Muestra el valor general y una fila por cada tipo de actividad con fecha distinta.

## Escenarios Inferidos

### Consultar las fechas de activación

Pasos propuestos:
1. Abrir **Pasarela > parámetros** y pulsar **fecha de activación**.
2. La lista muestra primero **por defecto** (por ejemplo «3 días») y luego una fila por cada tipo con valor propio (por ejemplo «sv n crt (sin especificar)»: upload; «sv n crt sin ce»: 5 días).

Endpoints asociados:
- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

### Cambiar el valor general

Pasos propuestos:
1. Pulsar el botón de modificar el valor por defecto.
2. Indicar el **número de días antes del inicio de la actividad** (o la palabra `upload` para activar el día en que se hace la exportación).
3. Pulsar **guardar**.

Endpoints asociados:
- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

### Poner una fecha distinta a un tipo de actividad

Pasos propuestos:
1. Pulsar **nuevo**.
2. Elegir el tipo de actividad con tres desplegables encadenados — **asistentes** (n, agd, s, sg, sssc, sr), **actividad** (crt, ca, cv, cve, cv-crt) y **tipo de actividad** — e indicar el número de días (o `upload`).
3. Pulsar **guardar**. Para quitar la excepción, abrir la fila y pulsar **eliminar** (pide confirmación).

Endpoints asociados:
- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

## Endpoints Del Flujo

- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`

## Errores Conocidos

- `Falta valor por defecto`
- `Falta id_tipo_activ`
- `Falta valor de activación`

## Ruta de menú

- **Legacy:** dre > Pasarela > parámetros > fecha de activación
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > fecha de activación
