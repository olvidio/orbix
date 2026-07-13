---
id: "pasarela.pantalla.nombre_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Formulario nombre (Twig)"
controller: "frontend/pasarela/controller/nombre_ajax.php"
vistas:
  - "frontend\/pasarela\/view\/nombre_form.html.twig"
  - "frontend\/pasarela\/view\/nombre_form_nuevo.html.twig"
fragmentos_frontend:[]
endpoints:
  - "\/src\/pasarela\/nombre_excepcion_guardar"
  - "\/src\/pasarela\/nombre_excepcion_eliminar"
  - "\/src\/pasarela\/tipo_activ_txt_data"
capacidades:
  - "pasarela.nombre.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Formulario nombre (Twig)

Plantillas Twig de alta/edición de nombre; servidas por `nombre_ajax` (`form_modificar`/`form_nuevo`). El controller legacy `nombre_form.php` (actividadtarifas) no tiene callers vivos.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/nombre_ajax.php`

## Vistas Relacionadas

- `frontend/pasarela/view/nombre_form.html.twig`
- `frontend/pasarela/view/nombre_form_nuevo.html.twig`

## Endpoints Usados

- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

Formulario embebido en el flujo nombre.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
