---
id: "pasarela.pantalla.contribucion_no_duerme_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Dispatcher AJAX contribución no duerme"
controller: "frontend/pasarela/controller/contribucion_no_duerme_ajax.php"
vistas:
  - "frontend\/pasarela\/view\/contribucion_no_duerme_default_form.html.twig"
  - "frontend\/pasarela\/view\/contribucion_no_duerme_form.html.twig"
  - "frontend\/pasarela\/view\/contribucion_no_duerme_form_nuevo.html.twig"
fragmentos_frontend:[]
endpoints:
  - "\/src\/pasarela\/contribucion_no_duerme_lista"
  - "\/src\/pasarela\/contribucion_no_duerme_default_data"
  - "\/src\/pasarela\/contribucion_no_duerme_default_guardar"
  - "\/src\/pasarela\/contribucion_no_duerme_excepcion_guardar"
  - "\/src\/pasarela\/contribucion_no_duerme_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
capacidades:
  - "pasarela.contribucion_no_duerme.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Dispatcher AJAX contribución no duerme

Dispatcher AJAX análogo a activación para el parámetro `contribucion_no_duerme`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`

## Vistas Relacionadas

- `frontend/pasarela/view/contribucion_no_duerme_default_form.html.twig`
- `frontend/pasarela/view/contribucion_no_duerme_form.html.twig`
- `frontend/pasarela/view/contribucion_no_duerme_form_nuevo.html.twig`

## Endpoints Usados

- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

Fragmento AJAX; no tiene menú propio.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
