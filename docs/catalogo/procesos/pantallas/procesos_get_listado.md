---
id: "procesos.pantalla.procesos_get_listado"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Procesos Get Listado"
controller: "frontend/procesos/controller/procesos_get_listado.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/procesos_get_listado"]
capacidades: ["procesos.procesos_get_listado.gestionar"]
campos: []
acciones: ["fnjs_eliminar", "fnjs_modificar"]
estado_revision: "revisado"
---

# Procesos Get Listado

Fragmento AJAX que renderiza la tabla tabular de fases/tareas del proceso con acciones para modificar y eliminar cada tarea.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/procesos_get_listado.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/procesos_get_listado`

## Capacidades Relacionadas

- `procesos.procesos_get_listado.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_eliminar`
- `fnjs_modificar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
