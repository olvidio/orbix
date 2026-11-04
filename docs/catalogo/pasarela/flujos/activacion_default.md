---
id: "pasarela.activacion_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Editar activación por defecto"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: ["pasarela.pantalla.activacion_ajax"]
fragmentos:
  - "pasarela.pantalla.activacion_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/activacion_default_data"
  - "\/src\/pasarela\/activacion_default_guardar"
estado_revision: "revisado"
---

# Flujo - Editar activación por defecto

## Objetivo De Usuario

Cambiar el valor global de días de activación (o `upload`).

## Punto De Entrada

Dentro de **fecha de activación**, con el botón de modificar el valor por defecto.

## Escenarios Inferidos

### Cambiar la fecha de activación general

Pasos propuestos:
1. En **fecha de activación**, abrir el valor por defecto.
2. Indicar el **número de días antes del inicio de la actividad** (o `upload`).
3. Pulsar **guardar** (o **cancel**). Si se deja vacío avisa de que falta el valor por defecto.

Endpoints asociados:
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`

## Endpoints Del Flujo

- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`

## Errores Conocidos

- `Falta valor por defecto`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
