---
id: "pasarela.activacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar fecha de activación"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: []
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

`activacion_lista.php` desde `parametros_menu`.

## Escenarios

### Listar
1. Abrir fecha de activación.
2. Carga automática vía `activacion_lista`.

### Editar default
1. Pulsar modificar default → `form_default`.
2. Guardar → `activacion_default_guardar`.

### Excepción
1. Nueva fila o editar → selector tipo + valor.
2. Guardar/eliminar vía endpoints de excepción.

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

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
