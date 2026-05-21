---
id: "actividades.pantalla.actividad_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Actividad Select"
controller: "frontend/actividades/controller/actividad_select.php"
vistas: ["frontend/actividades/view/actividad_select.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_select.php", "frontend/actividades/controller/actividad_ver.php"]
endpoints: ["/src/actividades/actividad_select_datos"]
capacidades: ["actividades.actividad_select.gestionar"]
campos: ["form.id_dossier", "form.mod", "form.queSel", "html.b_buscar", "html.id_dossier", "html.mod", "html.queSel", "post.Gstack", "post.continuar", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.fases_off", "post.fases_on", "post.filtro_lugar", "post.id_tipo_activ", "post.id_ubi", "post.modo", "post.nom_activ", "post.periodo", "post.publicado", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.scroll_id", "post.sel", "post.ssfsv", "post.stack", "post.status", "post.year"]
acciones: ["button:. _(", "fnjs_borrar", "fnjs_buscar", "fnjs_enviar_formulario", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "generado"
---

# Actividad Select

Lista de actividades que cumplen con los filtros de actividad_que.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/actividad_select.php`

## Vistas Relacionadas

- `frontend/actividades/view/actividad_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_select.php`
- `frontend/actividades/controller/actividad_ver.php`

## Endpoints Usados

- `/src/actividades/actividad_select_datos`

## Capacidades Relacionadas

- `actividades.actividad_select.gestionar`

## Campos Detectados

- `form.id_dossier`
- `form.mod`
- `form.queSel`
- `html.b_buscar`
- `html.id_dossier`
- `html.mod`
- `html.queSel`
- `post.Gstack`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.scroll_id`
- `post.sel`
- `post.ssfsv`
- `post.stack`
- `post.status`
- `post.year`

## Acciones Detectadas

- `button:. _(`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
