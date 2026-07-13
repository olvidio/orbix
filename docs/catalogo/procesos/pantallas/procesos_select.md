---
id: "procesos.pantalla.procesos_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Procesos Select"
controller: "frontend/procesos/controller/procesos_select.php"
vistas: ["frontend/procesos/view/procesos_select.html.twig"]
fragmentos_frontend: ["frontend/procesos/controller/procesos_get.php", "frontend/procesos/controller/procesos_get_listado.php", "frontend/procesos/controller/procesos_ver.php"]
endpoints: ["/src/procesos/procesos_clonar", "/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_regenerar", "/src/procesos/procesos_select_data", "/src/procesos/procesos_update"]
capacidades: ["procesos.procesos.gestionar", "procesos.procesos_clonar.gestionar", "procesos.procesos_regenerar.gestionar", "procesos.procesos_select.gestionar"]
campos: ["post.refresh", "post.stack"]
acciones: []
estado_revision: "revisado"
---

# Procesos Select

Administración de tipos de proceso: desplegable de proceso, visualización en árbol o listado tabular de fases/tareas, alta y edición en ventana modal, clonado desde otro proceso y regeneración masiva de procesos en actividades.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/procesos_select.php`

## Vistas Relacionadas

- `frontend/procesos/view/procesos_select.html.twig`

## Fragmentos Frontend Relacionados

- `frontend/procesos/controller/procesos_get.php`
- `frontend/procesos/controller/procesos_get_listado.php`
- `frontend/procesos/controller/procesos_ver.php`

## Endpoints Usados

- `/src/procesos/procesos_clonar`
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_regenerar`
- `/src/procesos/procesos_select_data`
- `/src/procesos/procesos_update`

## Capacidades Relacionadas

- `procesos.procesos.gestionar`
- `procesos.procesos_clonar.gestionar`
- `procesos.procesos_regenerar.gestionar`
- `procesos.procesos_select.gestionar`

## Campos Detectados

- `post.refresh`
- `post.stack`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
