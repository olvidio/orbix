---
id: "pasarela.pantalla.contribucion_no_duerme_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "pasarela"
nombre: "Contribución no duerme"
controller: "frontend/pasarela/controller/contribucion_no_duerme_lista.php"
vistas:
  - "frontend\/pasarela\/view\/contribucion_no_duerme_lista.html.twig"
fragmentos_frontend:
  - "frontend\/pasarela\/controller\/contribucion_no_duerme_ajax.php"
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

# Contribución no duerme

Porcentaje de contribución para asistentes que no pernoctan, por defecto y por tipo.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/pasarela/controller/contribucion_no_duerme_lista.php`

## Vistas Relacionadas

- `frontend/pasarela/view/contribucion_no_duerme_lista.html.twig`

## Endpoints Usados

- `/src/pasarela/contribucion_no_duerme_lista`
- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

1. Desde parámetros pasarela.
2. Configurar default (0–100 %) y excepciones.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
