---
id: "actividades.pantalla.actividad_select"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Resultados buscar actividad"
controller: "frontend/actividades/controller/actividad_select.php"
vistas: ["frontend/actividades/view/actividad_select.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_ver.php"]
endpoints: ["/src/actividades/actividad_select_datos"]
capacidades: ["actividades.actividad_select.gestionar"]
campos: ["form.id_dossier", "form.mod", "form.queSel", "html.b_buscar", "html.id_dossier", "html.mod", "html.queSel", "post.Gstack", "post.continuar", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.fases_off", "post.fases_on", "post.filtro_lugar", "post.id_tipo_activ", "post.id_ubi", "post.modo", "post.nom_activ", "post.periodo", "post.publicado", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.scroll_id", "post.sel", "post.ssfsv", "post.stack", "post.status", "post.year"]
acciones: ["fnjs_borrar", "fnjs_buscar", "fnjs_enviar_formulario", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "revisado"
---

# Resultados buscar actividad

Pantalla de **resultados** tras `actividad_que` (o acceso directo con filtros en
URL). Lista actividades vía `actividad_select_datos`; construye tabla `Lista` con
enlaces firmados a ficha (`actividad_ver`), dossiers, importar/publicar según
`modo`/`que`. Si hay >200 filas pide `continuar=1` antes de mostrar.

Soporta selección (`sel`, `mod`, `queSel`) para devolver actividad al llamador,
modo importar (`modo=importar`) y publicar (`modo=publicar`).

## Tipo

- Subtipo: `pantalla_principal` (nav + filtros embebidos + tabla)
- Controller: `frontend/actividades/controller/actividad_select.php`
- Vista: `frontend/actividades/view/actividad_select.phtml`

## Endpoints Usados

- `/src/actividades/actividad_select_datos`

## Manual De Usuario

Ver [`manual/actividades.md`](../../../manual/actividades.md), *Buscar Y Abrir
Actividades*. Refinar filtros, abrir ficha, importar o publicar según el flujo
de entrada.

## Ruta de menú

Destino habitual de `actividad_que` (`que=ver`); también accesos directos con
`id_tipo_activ` prefijado (submenús *buscar activ*):

- **Legacy:** dre/Calendario > actividades > buscar activ (y subentradas sv/sf);
  entradas por colectivo (vsm, vsg, vsr, dagd, vest…).
- **Pills2:** ACTIVIDADES > Buscar actividad > crt/cv/ca n/agd/s/sss+; ESTUDIOS >
  Buscar actividades (`que=ver` genérico).
