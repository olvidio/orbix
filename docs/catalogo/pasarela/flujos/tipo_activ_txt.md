---
id: "pasarela.tipo_activ_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Texto descriptivo del tipo"
capacidad: "pasarela.tipo_activ_txt.gestionar"
pantallas_principales: []
fragmentos:
  - "pasarela.pantalla.activacion_ajax"
  - "pasarela.pantalla.contribucion_no_duerme_ajax"
  - "pasarela.pantalla.contribucion_reserva_ajax"
  - "pasarela.pantalla.nombre_ajax"
acciones: ["listar", "guardar", "eliminar"]
endpoints:
  - "\/src\/pasarela\/tipo_activ_txt_data"
estado_revision: "revisado"
---

# Flujo - Texto descriptivo del tipo

## Objetivo De Usuario

Mostrar etiqueta legible del tipo al editar excepciones.

## Punto De Entrada

No se abre desde el menú: aparece al editar una fila de cualquier parámetro de la pasarela.

## Escenarios Inferidos

### Ver el nombre del tipo al editar

Pasos propuestos:
1. Abrir una fila de un parámetro (fecha de activación, nombre o contribución).
2. El sistema muestra el nombre completo del tipo de actividad (sección, asistentes y actividad) para saber qué se está editando.

Endpoints asociados:
- `/src/pasarela/tipo_activ_txt_data`

## Endpoints Del Flujo

- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

Ninguno documentado.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
