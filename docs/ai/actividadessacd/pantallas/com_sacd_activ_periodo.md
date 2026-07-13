---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadessacd"
titulo: "Com Sacd Activ Periodo"
pantalla: "actividadessacd.pantalla.com_sacd_activ_periodo"
preguntas: ["Que se puede hacer en Com Sacd Activ Periodo?", "Que campos tiene Com Sacd Activ Periodo?", "Que acciones hay en Com Sacd Activ Periodo?"]
capacidades: ["actividadessacd.com_sacd_activ_periodo_page.gestionar", "actividadessacd.comunicacion_activ_sacd.gestionar", "actividadessacd.comunicacion_activ_sacd_enviar.gestionar"]
endpoints: ["/src/actividadessacd/com_sacd_activ_periodo_page_data", "/src/actividadessacd/comunicacion_activ_sacd_data", "/src/actividadessacd/comunicacion_activ_sacd_enviar"]
source: "docs/catalogo/actividadessacd/pantallas/com_sacd_activ_periodo.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Com Sacd Activ Periodo

## Resumen

Pantalla unificada para la comunicación de actividades a los sacd. Cubre dos entradas: (1) desde el menú, con formulario de periodo + botones **buscar** / **enviar mail**; y (2) desde `personas_select` con `que=un_sacd` + `sel[]`, que abre la pantalla y auto-dispara la búsqueda sobre el sacd elegido. El listado y el envío se cargan por AJAX contra `comunicacion_activ_sacd_data` y `comunicacion_activ_sacd_enviar`; la configuración inicial (`perm_mod_txt`) viene de `com_sacd_activ_periodo_page_data`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `actividadessacd.com_sacd_activ_periodo_page.gestionar`
- `actividadessacd.comunicacion_activ_sacd.gestionar`
- `actividadessacd.comunicacion_activ_sacd_enviar.gestionar`

## Endpoints Relacionados

- `/src/actividadessacd/com_sacd_activ_periodo_page_data`
- `/src/actividadessacd/comunicacion_activ_sacd_data`
- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
