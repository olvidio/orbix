---
id: "pasarela.pantalla.nombre_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Dispatcher AJAX nombre"
controller: "frontend/pasarela/controller/nombre_ajax.php"
vistas:
  - "frontend\/pasarela\/view\/nombre_form.html.twig"
  - "frontend\/pasarela\/view\/nombre_form_nuevo.html.twig"
fragmentos_frontend:[]
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

# Dispatcher AJAX nombre

Dispatcher AJAX para el parámetro `nombre`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/nombre_ajax.php`

## Vistas Relacionadas

- `frontend/pasarela/view/nombre_form.html.twig`
- `frontend/pasarela/view/nombre_form_nuevo.html.twig`

## Endpoints Usados

- `/src/pasarela/nombre_lista`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

Fragmento AJAX; renderiza formularios Twig inline.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
