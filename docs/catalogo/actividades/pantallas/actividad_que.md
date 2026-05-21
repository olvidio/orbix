---
id: "actividades.pantalla.actividad_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Actividad Que"
controller: "frontend/actividades/controller/actividad_que.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_select.php", "frontend/actividades/controller/lista_activ.php", "frontend/asistentes/controller/lista_asis_conjunto_activ.php"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_que_filtros", "/src/actividades/actividad_tipo_get", "/src/procesos/actividad_que_fases_ajax"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_que_filtros.gestionar", "actividades.actividad_tipo.gestionar"]
campos: ["form.dl_org", "form.dl_propia", "form.entrada", "form.extendida", "form.filtro_lugar", "form.id_tipo_activ", "form.id_ubi", "form.isfsv", "form.modo", "form.opcion_sel", "form.publicado", "form.salida", "form.selected", "form.sfsv", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.extendida", "post.fases_off", "post.fases_on", "post.filtro_lugar", "post.id_tipo_activ", "post.id_ubi", "post.listar_asistentes", "post.modo", "post.nom_activ", "post.periodo", "post.publicado", "post.que", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.snom_tipo", "post.stack", "post.status", "post.year"]
acciones: []
estado_revision: "generado"
---

# Actividad Que

Pantalla para escoger los filtros que determinan una busqueda de actividades.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/actividad_que.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_select.php`
- `frontend/actividades/controller/lista_activ.php`
- `frontend/asistentes/controller/lista_asis_conjunto_activ.php`

## Endpoints Usados

- `/src/actividades/actividad_que_datos`
- `/src/actividades/actividad_que_filtros`
- `/src/actividades/actividad_tipo_get`
- `/src/procesos/actividad_que_fases_ajax`

## Capacidades Relacionadas

- `actividades.actividad_que.gestionar`
- `actividades.actividad_que_filtros.gestionar`
- `actividades.actividad_tipo.gestionar`

## Campos Detectados

- `form.dl_org`
- `form.dl_propia`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.id_tipo_activ`
- `form.id_ubi`
- `form.isfsv`
- `form.modo`
- `form.opcion_sel`
- `form.publicado`
- `form.salida`
- `form.selected`
- `form.sfsv`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.extendida`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.listar_asistentes`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.que`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.snom_tipo`
- `post.stack`
- `post.status`
- `post.year`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
