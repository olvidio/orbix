---
id: "pasarela.pantalla.exportar_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "pasarela"
nombre: "Exportar actividades"
controller: "frontend/pasarela/controller/exportar_que.php"
vistas:
  - "frontend\/pasarela\/view\/exportar_que.html.twig"
fragmentos_frontend:
  - "frontend\/pasarela\/controller\/exportar_select.php"
endpoints:
  - "\/src\/pasarela\/exportar_que_actividad_tipo_html"
  - "\/src\/pasarela\/exportar_actividades_data"
capacidades:
  - "pasarela.exportar_actividades.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Exportar actividades

Filtros (tipo, periodo, casas) y lanzamiento de la exportación de actividades hacia el exterior.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/pasarela/controller/exportar_que.php`

## Vistas Relacionadas

- `frontend/pasarela/view/exportar_que.html.twig`

## Endpoints Usados

- `/src/pasarela/exportar_que_actividad_tipo_html`
- `/src/pasarela/exportar_actividades_data`

## Manual De Usuario

1. Abrir Pasarela > exportar actividades.
2. Elegir tipo, periodo y casas.
3. Ejecutar exportación; la tabla se carga vía AJAX (`exportar_select`).

## Ruta de menú

- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades
