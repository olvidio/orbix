---
id: "procesos.pantalla.fases_activ_cambio"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Fases Activ Cambio"
controller: "frontend/procesos/controller/fases_activ_cambio.php"
vistas: ["frontend/procesos/view/fases_activ_cambio.html.twig"]
fragmentos_frontend: ["frontend/procesos/controller/fases_activ_cambio_lista.php"]
endpoints: ["/src/actividades/actividad_tipo_get", "/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_tipo_html", "/src/procesos/fases_activ_cambio_update"]
capacidades: ["procesos.fases_activ_cambio.gestionar", "procesos.fases_activ_cambio_tipo_html.gestionar"]
campos: ["form.accion", "form.dl_propia", "form.empiezamax", "form.empiezamin", "form.entrada", "form.extendida", "form.id_fase_nueva", "form.id_fase_sel", "form.id_tipo_activ", "form.modo", "form.periodo", "form.salida", "form.year", "post.dl_propia", "post.empiezamax", "post.empiezamin", "post.fin", "post.id_fase_nueva", "post.id_tipo_activ", "post.inicio", "post.periodo", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.stack", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Fases Activ Cambio

Cambio de fase masivo en actividades: selector de tipo de actividad, filtro DL propia/otras, periodo, fase destino y acción marcar/desmarcar; muestra listado de actividades candidatas y permite aplicar el cambio.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/fases_activ_cambio.php`

## Vistas Relacionadas

- `frontend/procesos/view/fases_activ_cambio.html.twig`

## Fragmentos Frontend Relacionados

- `frontend/procesos/controller/fases_activ_cambio_lista.php`

## Endpoints Usados

- `/src/actividades/actividad_tipo_get`
- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_tipo_html`
- `/src/procesos/fases_activ_cambio_update`

## Capacidades Relacionadas

- `procesos.fases_activ_cambio.gestionar`
- `procesos.fases_activ_cambio_tipo_html.gestionar`

## Campos Detectados

- `form.accion`
- `form.dl_propia`
- `form.empiezamax`
- `form.empiezamin`
- `form.entrada`
- `form.extendida`
- `form.id_fase_nueva`
- `form.id_fase_sel`
- `form.id_tipo_activ`
- `form.modo`
- `form.periodo`
- `form.salida`
- `form.year`
- `post.dl_propia`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_fase_nueva`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.stack`
- `post.year`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** Calendario > actividades > cambiar de fase; dre > actividades > cambiar de fase
- **Pills2:** ATENCIÓN SACD > Actividades > cambiar de fase; dre > actividades > cambiar de fase; Calendario > actividades > cambiar de fase; ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades
