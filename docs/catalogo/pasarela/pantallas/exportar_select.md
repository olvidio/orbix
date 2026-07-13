---
id: "pasarela.pantalla.exportar_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Resultado exportación actividades"
controller: "frontend/pasarela/controller/exportar_select.php"
vistas:[]
fragmentos_frontend:[]
endpoints:
  - "\/src\/pasarela\/exportar_actividades_data"
capacidades:
  - "pasarela.exportar_actividades.gestionar"
campos: []
acciones: []
estado_revision: "revisado"
---

# Resultado exportación actividades

Calcula periodo en frontend (`Periodo`) y muestra la tabla HTML devuelta por `exportar_actividades_data`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/exportar_select.php`

## Vistas Relacionadas

No aplica (respuesta HTML generada en controller).

## Endpoints Usados

- `/src/pasarela/exportar_actividades_data`

## Manual De Usuario

Fragmento AJAX invocado desde `exportar_que`.

## Ruta de menú

- sin entrada de menú en el índice (fragmento AJAX de `exportar_que`).
