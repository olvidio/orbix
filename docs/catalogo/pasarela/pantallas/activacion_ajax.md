---
id: "pasarela.pantalla.activacion_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Dispatcher AJAX activación"
controller: "frontend/pasarela/controller/activacion_ajax.php"
vistas:
  - "frontend\/pasarela\/view\/activacion_default_form.html.twig"
  - "frontend\/pasarela\/view\/activacion_form.html.twig"
  - "frontend\/pasarela\/view\/activacion_form_nuevo.html.twig"
fragmentos_frontend:[]
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

# Dispatcher AJAX activación

Orquesta las peticiones AJAX de activación según `que` (lista, form_default, form_modificar, form_nuevo, update, eliminar).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/activacion_ajax.php`

## Vistas Relacionadas

- `frontend/pasarela/view/activacion_default_form.html.twig`
- `frontend/pasarela/view/activacion_form.html.twig`
- `frontend/pasarela/view/activacion_form_nuevo.html.twig`

## Endpoints Usados

- `/src/pasarela/activacion_lista`
- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

No es pantalla autónoma; responde HTML o JSON al JS de `activacion_lista`.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
