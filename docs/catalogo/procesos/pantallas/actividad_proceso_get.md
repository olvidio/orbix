---
id: "procesos.pantalla.actividad_proceso_get"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Actividad Proceso Get"
controller: "frontend/procesos/controller/actividad_proceso_get.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/actividad_proceso_get"]
capacidades: ["procesos.actividad_proceso.gestionar"]
campos: ["html.b_guardar", "html.completado", "html.observ"]
acciones: ["fnjs_guardar"]
estado_revision: "revisado"
---

# Actividad Proceso Get

Fragmento AJAX que renderiza la tabla de tareas del proceso de una actividad (fase, tarea, responsable, completado, observaciones y botón guardar).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/actividad_proceso_get.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/actividad_proceso_get`

## Capacidades Relacionadas

- `procesos.actividad_proceso.gestionar`

## Campos Detectados

- `html.b_guardar`
- `html.completado`
- `html.observ`

## Acciones Detectadas

- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
