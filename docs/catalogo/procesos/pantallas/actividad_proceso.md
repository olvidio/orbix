---
id: "procesos.pantalla.actividad_proceso"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Actividad Proceso"
controller: "frontend/procesos/controller/actividad_proceso.php"
vistas: []
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/procesos/controller/actividad_proceso_get.php"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_generar", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
capacidades: ["procesos.actividad_proceso.gestionar", "procesos.actividad_proceso_generar.gestionar"]
campos: ["form.completado", "form.force", "form.id_item", "form.observ", "post.id_activ", "post.sel"]
acciones: []
estado_revision: "generado"
---

# Actividad Proceso

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/actividad_proceso.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
