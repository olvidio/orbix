---
id: "actividadessacd.pantalla.com_sacd_activ_periodo"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadessacd"
nombre: "Com Sacd Activ Periodo"
controller: "frontend/actividadessacd/controller/com_sacd_activ_periodo.php"
vistas: ["frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
fragmentos_frontend: ["frontend/actividadessacd/controller/com_sacd_txt.php"]
endpoints: ["/src/actividadessacd/com_sacd_activ_periodo_page_data", "/src/actividadessacd/comunicacion_activ_sacd_data", "/src/actividadessacd/comunicacion_activ_sacd_enviar"]
capacidades: ["actividadessacd.com_sacd_activ_periodo_page.gestionar", "actividadessacd.comunicacion_activ_sacd.gestionar", "actividadessacd.comunicacion_activ_sacd_enviar.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.year", "post.id_nom", "post.periodo", "post.propuesta", "post.que", "post.sel", "post.year"]
acciones: ["fnjs_cancelar", "fnjs_construir_listado", "fnjs_enviar_mails", "fnjs_esc_html", "fnjs_left_side_hide", "fnjs_parse_rta_txt", "fnjs_pintar_sacds", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Com Sacd Activ Periodo

Pantalla unificada para la comunicacion de actividades a los sacd.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`

## Vistas Relacionadas

- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadessacd/controller/com_sacd_txt.php`

## Endpoints Usados

- `/src/actividadessacd/com_sacd_activ_periodo_page_data`
- `/src/actividadessacd/comunicacion_activ_sacd_data`
- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

## Capacidades Relacionadas

- `actividadessacd.com_sacd_activ_periodo_page.gestionar`
- `actividadessacd.comunicacion_activ_sacd.gestionar`
- `actividadessacd.comunicacion_activ_sacd_enviar.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `post.id_nom`
- `post.periodo`
- `post.propuesta`
- `post.que`
- `post.sel`
- `post.year`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_construir_listado`
- `fnjs_enviar_mails`
- `fnjs_esc_html`
- `fnjs_left_side_hide`
- `fnjs_parse_rta_txt`
- `fnjs_pintar_sacds`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
