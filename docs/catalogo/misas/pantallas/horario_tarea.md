---
id: "misas.pantalla.horario_tarea"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "misas"
nombre: "Horario Tarea"
controller: "frontend/misas/controller/horario_tarea.php"
vistas: ["frontend/misas/view/horario_tarea.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/guardar_horario", "/src/misas/horario_tarea_data", "/src/misas/quitar_horario"]
capacidades: ["misas.guardar_horario.gestionar", "misas.horario_tarea.gestionar", "misas.quitar_horario.gestionar"]
campos: ["form.id_item", "form.t_end", "form.t_start", "post.id_item_h"]
acciones: ["fnjs_guardar_horario", "fnjs_quitar_horario"]
estado_revision: "revisado"
---

# Horario tarea

Modal para editar hora inicio/fin de una tarea en plantilla (`horario_tarea_data`, `guardar_horario`).

## Tipo

- Subtipo: `modal`


- Controller: `frontend/misas/controller/horario_tarea.php`

## Vistas Relacionadas

- `frontend/misas/view/horario_tarea.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/guardar_horario`
- `/src/misas/horario_tarea_data`
- `/src/misas/quitar_horario`

## Capacidades Relacionadas

- `misas.guardar_horario.gestionar`
- `misas.horario_tarea.gestionar`
- `misas.quitar_horario.gestionar`

## Campos Detectados

- `form.id_item`
- `form.t_end`
- `form.t_start`
- `post.id_item_h`

## Acciones Detectadas

- `fnjs_guardar_horario`
- `fnjs_quitar_horario`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
