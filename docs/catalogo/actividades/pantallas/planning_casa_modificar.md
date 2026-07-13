---
id: "actividades.pantalla.planning_casa_modificar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Editar actividad desde planning"
controller: "frontend/actividades/controller/planning_casa_modificar.php"
vistas: ["frontend/actividades/view/actividad_form.html.twig", "frontend/actividades/view/_actividad_form_head.html.twig", "frontend/actividades/view/_actividad_form_body.html.twig", "frontend/actividades/view/_actividad_form_botones.html.twig"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_select_ubi.php"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
campos: ["form.dl_org", "form.isfsv", "form.ssfsv", "post.id_activ"]
acciones: ["fnjs_guardar"]
estado_revision: "revisado"
---

# Editar actividad desde planning

Fragmento con el **formulario de edición** de una actividad existente, incrustado
en el planning de casas (`planning_casa_que`). Recibe `id_activ`, carga entidad y
desplegables con `actividad_ver_datos`, bloque tipo con `actividad_que_datos` y
etiquetas de status con `actividad_status_labels_datos`. Reutiliza las plantillas
`_actividad_form_*` en modo `editar`; guardar llama a `actividad_editar` (JS compartido).

## Tipo

- Subtipo: `fragmento_ajax` (cargado en popup/div del planning)
- Controller: `frontend/actividades/controller/planning_casa_modificar.php`

## Manual De Usuario

Desde el planning por casas: clic en actividad → editar campos → guardar cambios.

## Ruta de menú

sin entrada de menú en el índice (popup desde planning):

- **Legacy / Pills2:** acceso vía Calendario/dre/… > planning > por casas
  (`planning_casa_que.php`).
