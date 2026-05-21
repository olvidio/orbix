---
id: "misas.pantalla.horario_tarea"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Horario Tarea"
controller: "frontend/misas/controller/horario_tarea.php"
vistas: ["frontend/misas/view/horario_tarea.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/guardar_horario", "/src/misas/horario_tarea_data", "/src/misas/quitar_horario"]
capacidades: ["misas.guardar_horario.gestionar", "misas.horario_tarea.gestionar", "misas.quitar_horario.gestionar"]
campos: ["form.id_item", "form.t_end", "form.t_start", "post.id_item_h"]
acciones: ["fnjs_guardar_horario", "fnjs_quitar_horario"]
estado_revision: "generado"
---

# Horario Tarea

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
