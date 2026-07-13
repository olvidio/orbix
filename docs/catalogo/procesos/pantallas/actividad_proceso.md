---
id: "procesos.pantalla.actividad_proceso"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Actividad Proceso"
controller: "frontend/procesos/controller/actividad_proceso.php"
vistas: ["frontend/procesos/view/actividad_proceso.html.twig"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/procesos/controller/actividad_proceso_get.php"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_generar", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
capacidades: ["procesos.actividad_proceso.gestionar", "procesos.actividad_proceso_generar.gestionar"]
campos: ["form.completado", "form.force", "form.id_item", "form.observ", "post.id_activ", "post.sel"]
acciones: []
estado_revision: "revisado"
---

# Actividad Proceso

Vista del proceso de una actividad concreta: tabla de fases/tareas con completado y observaciones, opción forzar, regenerar proceso (con permiso calendario) y enlace al dossier de la actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/actividad_proceso.php`

## Vistas Relacionadas

- `frontend/procesos/view/actividad_proceso.html.twig`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/procesos/controller/actividad_proceso_get.php`

## Endpoints Usados

- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_generar`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

## Capacidades Relacionadas

- `procesos.actividad_proceso.gestionar`
- `procesos.actividad_proceso_generar.gestionar`

## Campos Detectados

- `form.completado`
- `form.force`
- `form.id_item`
- `form.observ`
- `post.id_activ`
- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
