---
id: "ubiscamas.pantalla.lista_habitaciones"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubiscamas"
nombre: "Lista Habitaciones"
controller: "frontend/ubiscamas/controller/lista_habitaciones.php"
vistas: ["frontend/ubiscamas/view/lista_habitaciones.phtml"]
fragmentos_frontend: []
endpoints: ["/src/ubiscamas/actividad_habitaciones_lista"]
capacidades: ["ubiscamas.actividad_habitaciones.gestionar"]
campos: ["post.id_activ", "post.refresh", "post.sel"]
acciones: ["fnjs_actualizar", "fnjs_lista_distribucion", "fnjs_lista_nombres", "fnjs_update_div"]
estado_revision: "revisado"
---

# Lista Habitaciones

Asignación visual de asistentes a camas de una actividad (dossier actividad, enlace `camas`).

## Tipo

- Subtipo: `fragmento_ajax`

- Controller: `frontend/ubiscamas/controller/lista_habitaciones.php`

## Vistas Relacionadas

- `frontend/ubiscamas/view/lista_habitaciones.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubiscamas/actividad_habitaciones_lista`

## Capacidades Relacionadas

- `ubiscamas.actividad_habitaciones.gestionar`

## Campos Detectados

- `post.id_activ`
- `post.refresh`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_lista_distribucion`
- `fnjs_lista_nombres`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
