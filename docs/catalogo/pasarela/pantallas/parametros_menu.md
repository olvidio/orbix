---
id: "pasarela.pantalla.parametros_menu"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "pasarela"
nombre: "Parámetros pasarela"
controller: "frontend/pasarela/controller/parametros_menu.php"
vistas:
  - "frontend\/pasarela\/view\/parametros_menu.html.twig"
fragmentos_frontend:
  - "frontend\/pasarela\/controller\/activacion_lista.php"
  - "frontend\/pasarela\/controller\/nombre_lista.php"
  - "frontend\/pasarela\/controller\/contribucion_no_duerme_lista.php"
  - "frontend\/pasarela\/controller\/contribucion_reserva_lista.php"
endpoints:[]
capacidades:[]
campos: []
acciones: []
estado_revision: "revisado"
---

# Parámetros pasarela

Menú de configuración de la pasarela de exterior: enlaces a fecha de activación, nombres particulares, contribución no duerme y contribución reserva.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/pasarela/controller/parametros_menu.php`

## Vistas Relacionadas

- `frontend/pasarela/view/parametros_menu.html.twig`

## Endpoints Usados

Ninguno directo (solo enlaces a subpantallas).

## Manual De Usuario

1. Abrir desde el menú Pasarela > parámetros.
2. Elegir el parámetro a configurar.
3. En cada subpantalla gestionar valor por defecto y excepciones por tipo de actividad.

## Ruta de menú

- **Legacy:** dre > Pasarela > parámetros
- **Pills2:** dre > Pasarela > parámetros; ACTIVIDADES > Pasarela > parámetros
