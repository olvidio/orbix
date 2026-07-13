---
id: "ubis.pantalla.calendario_periodos_get2"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Calendario Periodos Get2"
controller: "frontend/ubis/controller/calendario_periodos_get2.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/calendario_periodos_get2_data"]
capacidades: ["ubis.calendario_periodos_get2.gestionar"]
campos: ["post.id_ubi", "post.year"]
acciones: ["fnjs_modificar"]
estado_revision: "revisado"
---

# Calendario Periodos Get2

Tabla AJAX de periodos de calendario de una casa en un año con aviso de solapes.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/calendario_periodos_get2.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/calendario_periodos_get2_data`

## Capacidades Relacionadas

- `ubis.calendario_periodos_get2.gestionar`

## Campos Detectados

- `post.id_ubi`
- `post.year`

## Acciones Detectadas

- `fnjs_modificar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
