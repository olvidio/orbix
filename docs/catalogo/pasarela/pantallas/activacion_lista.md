---
id: "pasarela.pantalla.activacion_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "pasarela"
nombre: "Fecha de activación"
controller: "frontend/pasarela/controller/activacion_lista.php"
vistas:
  - "frontend\/pasarela\/view\/activacion_lista.html.twig"
fragmentos_frontend:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
endpoints:
  - "\/src\/pasarela\/activacion_lista"
  - "\/src\/pasarela\/activacion_default_data"
  - "\/src\/pasarela\/activacion_default_guardar"
  - "\/src\/pasarela\/activacion_excepcion_guardar"
  - "\/src\/pasarela\/activacion_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
capacidades:
  - "pasarela.activacion.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Fecha de activación

Gestiona cuándo se activa cada tipo de actividad en la pasarela (días antes del inicio o `upload`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/pasarela/controller/activacion_lista.php`

## Vistas Relacionadas

- `frontend/pasarela/view/activacion_lista.html.twig`

## Endpoints Usados

- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

1. Desde parámetros pasarela, abrir «fecha de activación».
2. Editar valor por defecto o añadir excepciones por tipo.
3. Guardar o eliminar filas.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
