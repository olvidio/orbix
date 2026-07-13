---
id: "pasarela.pantalla.contribucion_reserva_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "pasarela"
nombre: "Contribución reserva"
controller: "frontend/pasarela/controller/contribucion_reserva_lista.php"
vistas:
  - "frontend\/pasarela\/view\/contribucion_reserva_lista.html.twig"
fragmentos_frontend:
  - "frontend\/pasarela\/controller\/contribucion_reserva_ajax.php"
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

# Contribución reserva

Porcentaje de contribución en reserva de plaza, por defecto y por tipo de actividad.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/pasarela/controller/contribucion_reserva_lista.php`

## Vistas Relacionadas

- `frontend/pasarela/view/contribucion_reserva_lista.html.twig`

## Endpoints Usados

- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`
- `/src/pasarela/tipo_activ_txt_data`

## Manual De Usuario

1. Desde parámetros pasarela.
2. Configurar default y excepciones.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).
