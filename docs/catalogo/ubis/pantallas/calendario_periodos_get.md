---
id: "ubis.pantalla.calendario_periodos_get"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Calendario Periodos Get"
controller: "frontend/ubis/controller/calendario_periodos_get.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/calendario_periodos_get_data"]
capacidades: ["ubis.calendario_periodos_get.gestionar"]
campos: ["post.id_ubi"]
acciones: ["fnjs_grabar"]
estado_revision: "revisado"
---

# Calendario Periodos Get

Vista AJAX legacy de periodos de calendario con acciones grabar y borrar inline.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/calendario_periodos_get.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/calendario_periodos_get_data`

## Capacidades Relacionadas

- `ubis.calendario_periodos_get.gestionar`

## Campos Detectados

- `post.id_ubi`

## Acciones Detectadas

- `fnjs_grabar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
