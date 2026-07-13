---
id: "pasarela.pantalla.contribucion_reserva_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Dispatcher AJAX contribución reserva"
controller: "frontend/pasarela/controller/contribucion_reserva_ajax.php"
vistas:
  - "frontend\/pasarela\/view\/contribucion_reserva_default_form.html.twig"
  - "frontend\/pasarela\/view\/contribucion_reserva_form.html.twig"
  - "frontend\/pasarela\/view\/contribucion_reserva_form_nuevo.html.twig"
fragmentos_frontend:[]
endpoints:
  - "\/src\/pasarela\/contribucion_reserva_lista"
  - "\/src\/pasarela\/contribucion_reserva_default_data"
  - "\/src\/pasarela\/contribucion_reserva_default_guardar"
  - "\/src\/pasarela\/contribucion_reserva_excepcion_guardar"
  - "\/src\/pasarela\/contribucion_reserva_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
capacidades:
  - "pasarela.contribucion_reserva.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Dispatcher AJAX contribución reserva

Dispatcher AJAX para `contribucion_reserva`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Vistas Relacionadas

- `frontend/pasarela/view/contribucion_reserva_default_form.html.twig`
- `frontend/pasarela/view/contribucion_reserva_form.html.twig`
- `frontend/pasarela/view/contribucion_reserva_form_nuevo.html.twig`

## Endpoints Usados

- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

Fragmento AJAX embebido.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
