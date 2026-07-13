---
id: "pasarela.pantalla.nombre_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "pasarela"
nombre: "Nombres de actividades particulares"
controller: "frontend/pasarela/controller/nombre_lista.php"
vistas:
  - "frontend\/pasarela\/view\/nombre_lista.html.twig"
fragmentos_frontend:
  - "frontend\/pasarela\/controller\/nombre_ajax.php"
endpoints:
  - "\/src\/pasarela\/nombre_lista"
  - "\/src\/pasarela\/nombre_excepcion_guardar"
  - "\/src\/pasarela\/nombre_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
capacidades:
  - "pasarela.nombre.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Nombres de actividades particulares

Nombres exportados distintos al tipo genérico, solo por excepción (sin valor default).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/pasarela/controller/nombre_lista.php`

## Vistas Relacionadas

- `frontend/pasarela/view/nombre_lista.html.twig`

## Endpoints Usados

- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

1. Desde parámetros pasarela.
2. Añadir, editar o eliminar nombres por tipo.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
