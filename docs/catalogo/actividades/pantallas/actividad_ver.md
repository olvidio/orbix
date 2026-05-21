---
id: "actividades.pantalla.actividad_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Actividad Ver"
controller: "frontend/actividades/controller/actividad_ver.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/controller/actividad_ver.php", "frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/actividades/actividad_nivel_stgr_default_datos", "/src/actividades/actividad_permiso_crear_datos", "/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
capacidades: ["actividades.actividad_nivel_stgr_default.gestionar", "actividades.actividad_permiso_crear.gestionar", "actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
campos: ["form.dl_org", "form.isfsv", "form.ssfsv", "post.id_activ", "post.id_tipo_activ", "post.mod", "post.obj_pau", "post.refresh", "post.sactividad", "post.sasistentes", "post.sel"]
acciones: []
estado_revision: "generado"
---

# Actividad Ver

Formulario de ver/editar una actividad (tambien para crear nueva o cambiar tipo).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/actividad_ver.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/controller/actividad_ver.php`
- `frontend/dossiers/controller/dossiers_ver.php`

## Endpoints Usados

- `/src/actividades/actividad_nivel_stgr_default_datos`
- `/src/actividades/actividad_permiso_crear_datos`
- `/src/actividades/actividad_que_datos`
- `/src/actividades/actividad_status_labels_datos`
- `/src/actividades/actividad_ver_datos`

## Capacidades Relacionadas

- `actividades.actividad_nivel_stgr_default.gestionar`
- `actividades.actividad_permiso_crear.gestionar`
- `actividades.actividad_que.gestionar`
- `actividades.actividad_status_labels.gestionar`
- `actividades.actividad_ver.gestionar`

## Campos Detectados

- `form.dl_org`
- `form.isfsv`
- `form.ssfsv`
- `post.id_activ`
- `post.id_tipo_activ`
- `post.mod`
- `post.obj_pau`
- `post.refresh`
- `post.sactividad`
- `post.sasistentes`
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
