---
id: "procesos.pantalla.fases_activ_cambio"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Fases Activ Cambio"
controller: "frontend/procesos/controller/fases_activ_cambio.php"
vistas: []
fragmentos_frontend: ["frontend/procesos/controller/fases_activ_cambio_lista.php"]
endpoints: ["/src/actividades/actividad_tipo_get", "/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_tipo_html", "/src/procesos/fases_activ_cambio_update"]
capacidades: ["procesos.fases_activ_cambio.gestionar", "procesos.fases_activ_cambio_tipo_html.gestionar"]
campos: ["form.accion", "form.dl_propia", "form.empiezamax", "form.empiezamin", "form.entrada", "form.extendida", "form.id_fase_nueva", "form.id_fase_sel", "form.id_tipo_activ", "form.modo", "form.periodo", "form.salida", "form.year", "post.dl_propia", "post.empiezamax", "post.empiezamin", "post.fin", "post.id_fase_nueva", "post.id_tipo_activ", "post.inicio", "post.periodo", "post.sactividad", "post.sactividad2", "post.sasistentes", "post.stack", "post.year"]
acciones: []
estado_revision: "generado"
---

# Fases Activ Cambio

Página para cambiar la fase a un grupo de actividades.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/fases_activ_cambio.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
