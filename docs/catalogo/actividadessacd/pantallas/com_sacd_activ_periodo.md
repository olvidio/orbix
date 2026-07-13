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
estado_revision: "revisado"
---

# Com Sacd Activ Periodo

Pantalla unificada para la comunicación de actividades a los sacd. Cubre dos entradas: (1) desde el
menú, con formulario de periodo + botones **buscar** / **enviar mail**; y (2) desde
`personas_select` con `que=un_sacd` + `sel[]`, que abre la pantalla y auto-dispara la búsqueda sobre
el sacd elegido. El listado y el envío se cargan por AJAX contra `comunicacion_activ_sacd_data` y
`comunicacion_activ_sacd_enviar`; la configuración inicial (`perm_mod_txt`) viene de
`com_sacd_activ_periodo_page_data`.

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

1. Seleccionar el periodo y pulsar **buscar**: se muestra, por cada sacd, la lista de actividades a
   comunicar y los textos de la carta.
2. Revisar el listado (incluye los "sacd de paso" cuando procede). Con permiso, se puede editar el
   texto base desde el fragmento de edición de textos (`com_sacd_txt`).
3. Pulsar **enviar mail** para encolar los correos (uno por sacd con copia al jefe de calendario, y
   otro para el ctr del sacd si tiene email). Requiere un periodo válido y el jefe de calendario
   configurado.

## Ruta de menú

- **Legacy:** dre > actividades > comunic. sacd · exterior > sacd > atención actividades
- **Pills2:** ATENCIÓN SACD > Actividades > Comunicación a los sacd

Con `propuesta=true` (lista de propuestas): Legacy y Pills2 → dre > propuestas > lista activ. sacd.
