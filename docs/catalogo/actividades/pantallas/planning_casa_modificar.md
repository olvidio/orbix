---
id: "actividades.pantalla.planning_casa_modificar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Planning Casa Modificar"
controller: "frontend/actividades/controller/planning_casa_modificar.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/controller/planning_casa_modificar.php"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
campos: ["form.dl_org", "form.isfsv", "form.ssfsv", "post.id_activ"]
acciones: []
estado_revision: "generado"
---

# Planning Casa Modificar

Formulario para modificar una actividad desde el planning de casas.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/planning_casa_modificar.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/controller/planning_casa_modificar.php`

## Endpoints Usados

- `/src/actividades/actividad_que_datos`
- `/src/actividades/actividad_status_labels_datos`
- `/src/actividades/actividad_ver_datos`

## Capacidades Relacionadas

- `actividades.actividad_que.gestionar`
- `actividades.actividad_status_labels.gestionar`
- `actividades.actividad_ver.gestionar`

## Campos Detectados

- `form.dl_org`
- `form.isfsv`
- `form.ssfsv`
- `post.id_activ`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
