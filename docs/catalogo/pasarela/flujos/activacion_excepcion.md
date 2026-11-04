---
id: "pasarela.activacion_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Excepción de activación por tipo"
capacidad: "pasarela.activacion.gestionar"
pantallas_principales: ["pasarela.pantalla.activacion_ajax"]
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

Dentro de **fecha de activación**, con **nuevo** o abriendo una fila existente.

## Escenarios Inferidos

### Añadir o cambiar la fecha de un tipo

Pasos propuestos:
1. Pulsar **nuevo**, o pulsar el valor de una fila (por ejemplo «5 días» o «upload»): se abre una ventana «Fecha de activación para: …» con el valor y los botones **guardar**, **eliminar** y **cancel**.
2. Elegir el tipo con los desplegables **asistentes**, **actividad** y **tipo de actividad** (cada uno depende del anterior).
3. Indicar el **número de días antes del inicio de la actividad** (o `upload`) y pulsar **guardar**. Si falta el tipo avisa «Tipo no válido»; si falta el valor, «debe indicar un periodo». Aviso: en la instancia de desarrollo (3-11-2026), guardar una excepción ya existente responde «respuesta: Operación no autorizada» (el formulario no envía el permiso que el servidor exige); alta nueva sin comprobar.

Endpoints asociados:
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

### Quitar la excepción de un tipo

Pasos propuestos:
1. Abrir la fila del tipo.
2. Pulsar **eliminar** y confirmar «¿Está seguro que quiere eliminar esta fila?».
3. El tipo vuelve a usar el valor general.

Endpoints asociados:
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Endpoints Del Flujo

- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta valor de activación`

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
