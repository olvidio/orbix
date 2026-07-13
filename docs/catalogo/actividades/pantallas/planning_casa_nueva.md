---
id: "actividades.pantalla.planning_casa_nueva"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Nueva actividad desde planning"
controller: "frontend/actividades/controller/planning_casa_nueva.php"
vistas: ["frontend/actividades/view/actividad_form.html.twig", "frontend/actividades/view/_actividad_form_head.html.twig", "frontend/actividades/view/_actividad_form_body.html.twig", "frontend/actividades/view/_actividad_form_botones.html.twig"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_select_ubi.php"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_ver_datos"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_ver.gestionar"]
campos: ["form.dl_org", "form.isfsv", "form.ssfsv", "post.id_ubi"]
acciones: ["fnjs_guardar"]
estado_revision: "revisado"
---

# Nueva actividad desde planning

Fragmento para **alta de actividad** en el planning de casas. Recibe `id_ubi` (casa
del calendario), precarga delegación y sf/sv del usuario, status inicial *proyecto*
y formulario vacío vía `actividad_ver_datos` (`id_activ=0`). Cascada de tipo con
`actividad_que_datos`. Guardar usa `actividad_nuevo` (JS compartido con ficha).

## Tipo

- Subtipo: `fragmento_ajax` (popup/div del planning)
- Controller: `frontend/actividades/controller/planning_casa_nueva.php`

## Manual De Usuario

En planning por casas: crear actividad en una casa/fecha → completar tipo y datos →
guardar.

## Ruta de menú

sin entrada de menú en el índice (popup desde planning):

- **Legacy / Pills2:** Calendario/dre/adl/… > planning > por casas o *nuevo planing*
  en estudio (`planning_casa_que.php`).
