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

Formularios `form_modificar` de parámetros pasarela.

## Escenarios

Con `id_tipo_activ` devuelve texto sf/sv + asistentes + actividad.

## Endpoints Del Flujo

- `/src/pasarela/tipo_activ_txt_data`

## Errores Conocidos

Ninguno documentado.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
