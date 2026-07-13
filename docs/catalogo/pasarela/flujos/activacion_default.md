---
id: "pasarela.activacion_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Editar activación por defecto"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: []
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

Desde listado activación, acción modificar default.

## Escenarios

1. `form_default` carga `activacion_default_data`.
2. Usuario guarda → `activacion_default_guardar`.

## Endpoints Del Flujo

- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`

## Errores Conocidos

- `Falta valor por defecto`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
